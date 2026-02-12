<?php

namespace App\Tests\Bridge\Brevo;

use App\Bridge\Brevo\Brevo;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Entity\Project;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

enum TestContactFormalTitle: string
{
    case MR = 'Mr';
}

class TestableBrevo extends Brevo
{
    public ?\DateTimeImmutable $frozenNow = null;

    public array $waitedSeconds = [];

    public function exposeBuildCampaignPayload(EmailingCampaign $campaign, string $htmlContent): array
    {
        return $this->buildCampaignPayload($campaign, $htmlContent);
    }

    public function exposeBuildContactAttributes(array $contact): array
    {
        return $this->buildContactAttributes($contact);
    }

    protected function getCurrentUtcDateTime(): \DateTimeImmutable
    {
        return $this->frozenNow ?? parent::getCurrentUtcDateTime();
    }

    protected function waitForSeconds(int $seconds): void
    {
        $this->waitedSeconds[] = $seconds;
    }
}

class BrevoTest extends TestCase
{
    public function testBuildCampaignPayloadContainsExpectedFields(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);
        $bridge = $this->createBridge(new MockHttpClient([]));

        $payload = $bridge->exposeBuildCampaignPayload($campaign, '<p>Hello</p>');

        $this->assertSame('Campaign Subject', $payload['name']);
        $this->assertSame('Campaign Subject', $payload['subject']);
        $this->assertSame('Campaign Sender', $payload['sender']['name']);
        $this->assertSame('sender@example.test', $payload['sender']['email']);
        $this->assertSame('<p>Hello</p>', $payload['htmlContent']);
        $this->assertSame('reply@example.test', $payload['replyTo']);
        $this->assertSame('Campaign preview', $payload['previewText']);
    }

    public function testBuildContactAttributesNormalizesBackedEnumsAndSkipsEmptyValues(): void
    {
        $bridge = $this->createBridge(new MockHttpClient([]));

        $attributes = $bridge->exposeBuildContactAttributes([
            'phone' => '+33601020304',
            'formalTitle' => TestContactFormalTitle::MR,
            'firstName' => '  ',
            'country' => null,
        ]);

        $this->assertSame([
            'PHONE' => '+33601020304',
            'FORMAL_TITLE' => 'Mr',
        ], $attributes);
    }

    public function testSendEmailCampaignSendsNowWhenThrottlingIsDisabled(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);
        $requests = [];

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use (&$requests): MockResponse {
            $requests[] = [$method, $url, $options];
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('GET' === $method && '/v3/contacts/folders' === $path) {
                return new MockResponse('{"folders":[{"id":42,"name":"Default"}]}');
            }

            if ('POST' === $method && '/v3/contacts/lists' === $path) {
                $requestBody = $this->decodeJsonRequestBody($options);
                $this->assertSame('openaction-campaign-12', $requestBody['name']);
                $this->assertSame(42, $requestBody['folderId']);

                return new MockResponse('{"id":99}');
            }

            if ('POST' === $method && '/v3/contacts/import' === $path) {
                $requestBody = $this->decodeJsonRequestBody($options);
                $this->assertSame([99], $requestBody['listIds']);
                $this->assertCount(2, $requestBody['jsonBody']);
                $this->assertTrue($requestBody['updateExistingContacts']);

                return new MockResponse('{}');
            }

            if ('POST' === $method && '/v3/emailCampaigns' === $path) {
                $requestBody = $this->decodeJsonRequestBody($options);
                $this->assertSame([99], $requestBody['recipients']['listIds']);
                $this->assertArrayNotHasKey('scheduledAt', $requestBody);

                return new MockResponse('{"id":201}');
            }

            if ('POST' === $method && '/v3/emailCampaigns/201/sendNow' === $path) {
                return new MockResponse('', ['http_code' => 204]);
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient);

        $this->assertSame('201', $bridge->sendEmailCampaign($campaign, '<p>Hello</p>', [
            ['email' => 'first@example.test'],
            ['email' => 'second@example.test'],
        ]));
        $this->assertCount(5, $requests);
    }

    public function testSendEmailCampaignUsesNativeBatchSendingWhenThrottlingApplies(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: 8);
        $createdListNames = [];
        $importedContactsPerList = [];
        $batchPayloads = [];
        $sendNowCalls = 0;
        $listId = 40;
        $campaignId = 200;

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use (&$createdListNames, &$importedContactsPerList, &$batchPayloads, &$sendNowCalls, &$listId, &$campaignId): MockResponse {
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('GET' === $method && '/v3/contacts/folders' === $path) {
                return new MockResponse('{"folders":[{"id":42,"name":"Default"}]}');
            }

            if ('POST' === $method && '/v3/contacts/lists' === $path) {
                $requestBody = $this->decodeJsonRequestBody($options);
                $createdListNames[] = $requestBody['name'];

                return new MockResponse('{"id":'.(++$listId).'}');
            }

            if ('POST' === $method && '/v3/contacts/import' === $path) {
                $requestBody = $this->decodeJsonRequestBody($options);
                $importedContactsPerList[] = count($requestBody['jsonBody'] ?? []);

                return new MockResponse('{}');
            }

            if ('POST' === $method && '/v3/emailCampaigns' === $path) {
                $requestBody = $this->decodeJsonRequestBody($options);
                $this->assertArrayNotHasKey('scheduledAt', $requestBody);

                return new MockResponse('{"id":'.(++$campaignId).'}');
            }

            if ('POST' === $method && '/v3/campaign/201/send-in-batch' === $path) {
                $batchPayloads[] = $this->decodeJsonRequestBody($options);

                return new MockResponse('', ['http_code' => 204]);
            }

            if ('POST' === $method && preg_match('#^/v3/emailCampaigns/\d+/sendNow$#', $path)) {
                ++$sendNowCalls;

                return new MockResponse('', ['http_code' => 204]);
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient);
        $bridge->frozenNow = new \DateTimeImmutable('2026-02-12 10:00:00', new \DateTimeZone('UTC'));

        $contacts = [];
        for ($index = 1; $index <= 16; ++$index) {
            $contacts[] = ['email' => sprintf('contact%d@example.test', $index)];
        }

        $this->assertSame('201', $bridge->sendEmailCampaign($campaign, '<p>Hello</p>', $contacts));

        $this->assertSame([
            'openaction-campaign-12',
        ], $createdListNames);
        $this->assertSame([16], $importedContactsPerList);
        $this->assertSame([
            [
                'schedule_date_time' => '2026-02-12 10:00:00',
                'is_raw_schedule_date_time' => true,
                'no_of_batches' => 2,
                'time_interval' => 30,
            ],
        ], $batchPayloads);
        $this->assertSame(0, $sendNowCalls);
    }

    public function testSendEmailCampaignUsesTenNativeBatchesWhenAudienceIsVeryLarge(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: 8);
        $batchPayload = null;

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use (&$batchPayload): MockResponse {
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('GET' === $method && '/v3/contacts/folders' === $path) {
                return new MockResponse('{"folders":[{"id":42,"name":"Default"}]}');
            }

            if ('POST' === $method && '/v3/contacts/lists' === $path) {
                return new MockResponse('{"id":99}');
            }

            if ('POST' === $method && '/v3/contacts/import' === $path) {
                return new MockResponse('{}');
            }

            if ('POST' === $method && '/v3/emailCampaigns' === $path) {
                return new MockResponse('{"id":201}');
            }

            if ('POST' === $method && '/v3/campaign/201/send-in-batch' === $path) {
                $batchPayload = $this->decodeJsonRequestBody($options);

                return new MockResponse('', ['http_code' => 204]);
            }

            if ('POST' === $method && preg_match('#^/v3/emailCampaigns/\d+/sendNow$#', $path)) {
                $this->fail('sendNow should not be called when native batch sending is enabled.');
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient);
        $bridge->frozenNow = new \DateTimeImmutable('2026-02-12 10:00:00', new \DateTimeZone('UTC'));

        $contacts = [];
        for ($index = 1; $index <= 81; ++$index) {
            $contacts[] = ['email' => sprintf('contact%d@example.test', $index)];
        }

        $this->assertSame('201', $bridge->sendEmailCampaign($campaign, '<p>Hello</p>', $contacts));
        $this->assertNotNull($batchPayload);
        $this->assertSame(10, $batchPayload['no_of_batches']);
        $this->assertSame(30, $batchPayload['time_interval']);
    }

    public function testGetEmailCampaignsStatsUsesExpectedPaginationParametersAndDateRange(): void
    {
        $requestedOffsets = [];
        $startDate = new \DateTimeImmutable('2026-02-10 09:30:00', new \DateTimeZone('UTC'));
        $endDate = new \DateTimeImmutable('2026-02-12 11:45:00', new \DateTimeZone('UTC'));

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use (&$requestedOffsets, $startDate, $endDate): MockResponse {
            $this->assertSame('GET', $method);
            $this->assertSame('/v3/emailCampaigns', (string) parse_url($url, PHP_URL_PATH));
            $this->assertSame('sent', $options['query']['status']);
            $this->assertSame('globalStats', $options['query']['statistics']);
            $this->assertSame(100, $options['query']['limit']);
            $this->assertSame('true', (string) $options['query']['excludeHtmlContent']);
            $this->assertSame($startDate->format(\DateTimeInterface::RFC3339_EXTENDED), $options['query']['startDate']);
            $this->assertSame($endDate->format(\DateTimeInterface::RFC3339_EXTENDED), $options['query']['endDate']);

            $offset = (int) $options['query']['offset'];
            $requestedOffsets[] = $offset;

            if (0 === $offset) {
                return new MockResponse('{"count":101,"campaigns":[{"id":10,"statistics":{"globalStats":{"delivered":12}}}]}');
            }

            if (100 === $offset) {
                return new MockResponse('{"count":101,"campaigns":[{"id":20,"statistics":{"globalStats":{"delivered":8}}}]}');
            }

            $this->fail('Unexpected offset '.$offset);
        });

        $bridge = $this->createBridge($httpClient);
        $this->assertSame([
            '10' => ['delivered' => 12],
            '20' => ['delivered' => 8],
        ], $bridge->getEmailCampaignsStats('test-api-key', $startDate, $endDate));
        $this->assertSame([0, 100], $requestedOffsets);
    }

    public function testGetEmailCampaignReportReturnsEmptyArray(): void
    {
        $bridge = $this->createBridge(new MockHttpClient([]));

        $this->assertSame([], $bridge->getEmailCampaignReport('test-api-key', '42'));
    }

    public function testRequestRetriesOnceOn429UsingResetHeader(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);

        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            static $listRequests = 0;
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('GET' === $method && '/v3/contacts/folders' === $path) {
                return new MockResponse('{"folders":[{"id":42}]}');
            }

            if ('POST' === $method && '/v3/contacts/lists' === $path) {
                ++$listRequests;

                if (1 === $listRequests) {
                    return new MockResponse(
                        '{"message":"Rate limit"}',
                        [
                            'http_code' => 429,
                            'response_headers' => ['x-sib-ratelimit-reset' => ['0']],
                        ],
                    );
                }

                return new MockResponse('{"id":99}');
            }

            if ('POST' === $method && '/v3/contacts/import' === $path) {
                return new MockResponse('{}');
            }

            if ('POST' === $method && '/v3/emailCampaigns' === $path) {
                return new MockResponse('{"id":201}');
            }

            if ('POST' === $method && '/v3/emailCampaigns/201/sendNow' === $path) {
                return new MockResponse('', ['http_code' => 204]);
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient);
        $bridge->sendEmailCampaign($campaign, '<p>Hello</p>', [['email' => 'first@example.test']]);

        $this->assertSame([1], $bridge->waitedSeconds);
    }

    public function testRequestThrowsAfterSecond429(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);

        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('GET' === $method && '/v3/contacts/folders' === $path) {
                return new MockResponse('{"folders":[{"id":42}]}');
            }

            if ('POST' === $method && '/v3/contacts/lists' === $path) {
                return new MockResponse(
                    '{"message":"Rate limit"}',
                    [
                        'http_code' => 429,
                        'response_headers' => ['x-sib-ratelimit-reset' => ['2']],
                    ],
                );
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('after one retry');
        $bridge->sendEmailCampaign($campaign, '<p>Hello</p>', [['email' => 'first@example.test']]);
    }

    public function testLocalRateLimiterBlocksRequests(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);

        $httpClient = new MockHttpClient(fn (): MockResponse => new MockResponse('{"folders":[{"id":42}]}'));
        $sendLimiter = $this->createLimiterFactory('send', 1);
        $statsLimiter = $this->createLimiterFactory('stats', 50);
        $bridge = $this->createBridge($httpClient, $sendLimiter, $statsLimiter);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('local rate limiter reached');
        $bridge->sendEmailCampaign($campaign, '<p>Hello</p>', [['email' => 'first@example.test']]);
    }

    private function createBridge(
        MockHttpClient $httpClient,
        ?RateLimiterFactory $sendCampaignRateLimiter = null,
        ?RateLimiterFactory $campaignStatsRateLimiter = null,
    ): TestableBrevo {
        return new TestableBrevo(
            new NullLogger(),
            $httpClient,
            $sendCampaignRateLimiter ?? $this->createLimiterFactory('send', 50),
            $campaignStatsRateLimiter ?? $this->createLimiterFactory('stats', 50),
            'openaction',
        );
    }

    private function createLimiterFactory(string $id, int $limit): RateLimiterFactory
    {
        return new RateLimiterFactory([
            'id' => $id,
            'policy' => 'sliding_window',
            'limit' => $limit,
            'interval' => '1 hour',
        ], new InMemoryStorage());
    }

    private function decodeJsonRequestBody(array $options): array
    {
        $body = $options['body'] ?? null;

        if (!is_string($body) || '' === $body) {
            $this->fail('Missing JSON request body.');
        }

        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            $this->fail('Invalid JSON request body.');
        }

        return $decoded;
    }

    private function createCampaignMock(?int $emailThrottlingPerHour): EmailingCampaign
    {
        $organization = $this->createMock(Organization::class);
        $organization->method('getName')->willReturn('OpenAction');
        $organization->method('getBrevoSenderEmail')->willReturn('sender@example.test');
        $organization->method('getBrevoApiKey')->willReturn('test-api-key');
        $organization->method('getEmailThrottlingPerHour')->willReturn($emailThrottlingPerHour);

        $project = $this->createMock(Project::class);
        $project->method('getOrganization')->willReturn($organization);

        $campaign = $this->createMock(EmailingCampaign::class);
        $campaign->method('getId')->willReturn(12);
        $campaign->method('getProject')->willReturn($project);
        $campaign->method('getFromName')->willReturn('Campaign Sender');
        $campaign->method('getSubject')->willReturn('Campaign Subject');
        $campaign->method('getReplyToEmail')->willReturn(null);
        $campaign->method('getFullFromEmail')->willReturn('reply@example.test');
        $campaign->method('getPreview')->willReturn('Campaign preview');

        return $campaign;
    }
}
