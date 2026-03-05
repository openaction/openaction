<?php

namespace App\Tests\Bridge\Brevo;

use App\Bridge\Brevo\Brevo;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Entity\Project;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Clock\MockClock;
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
    public function exposeBuildCampaignPayload(EmailingCampaign $campaign, string $htmlContent): array
    {
        return $this->buildCampaignPayload($campaign, $htmlContent);
    }

    public function exposeBuildContactAttributes(array $contact): array
    {
        return $this->buildContactAttributes($contact);
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
        $importProcessPolls = 0;

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use (&$requests, &$importProcessPolls): MockResponse {
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

                return new MockResponse('{"processId":78}', ['http_code' => 202]);
            }

            if ('GET' === $method && '/v3/processes/78' === $path) {
                ++$importProcessPolls;

                return new MockResponse('{"id":78,"status":"completed"}');
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
        $this->assertCount(6, $requests);
        $this->assertSame(1, $importProcessPolls);
        $this->assertSame('/v3/contacts/import', (string) parse_url($requests[2][1], PHP_URL_PATH));
        $this->assertSame('/v3/processes/78', (string) parse_url($requests[3][1], PHP_URL_PATH));
        $this->assertSame('/v3/emailCampaigns', (string) parse_url($requests[4][1], PHP_URL_PATH));
        $this->assertSame('/v3/emailCampaigns/201/sendNow', (string) parse_url($requests[5][1], PHP_URL_PATH));
    }

    public function testSyncCampaignContactsPollsImportProcessWithInitialAndRegularDelays(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);
        $processPolls = 0;
        $clock = new MockClock('2026-02-12 10:00:00', 'UTC');
        $beforeWait = (float) $clock->now()->format('U.u');

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use (&$processPolls): MockResponse {
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('POST' === $method && '/v3/contacts/import' === $path) {
                $requestBody = $this->decodeJsonRequestBody($options);
                $this->assertSame([99], $requestBody['listIds']);
                $this->assertCount(1, $requestBody['jsonBody']);

                return new MockResponse('{"processId":78}', ['http_code' => 202]);
            }

            if ('GET' === $method && '/v3/processes/78' === $path) {
                ++$processPolls;

                if (1 === $processPolls) {
                    return new MockResponse('{"id":78,"status":"queued"}');
                }

                return new MockResponse('{"id":78,"status":"completed"}');
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient, null, null, $clock);
        $bridge->syncCampaignContacts($campaign, 99, [['email' => 'first@example.test']]);

        $this->assertSame(2, $processPolls);
        $this->assertEqualsWithDelta($beforeWait + 75.0, (float) $clock->now()->format('U.u'), 0.0001);
    }

    public function testSyncCampaignContactsThrowsWhenImportProcessFails(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);

        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('POST' === $method && '/v3/contacts/import' === $path) {
                return new MockResponse('{"processId":78}', ['http_code' => 202]);
            }

            if ('GET' === $method && '/v3/processes/78' === $path) {
                return new MockResponse('{"id":78,"status":"failed","error":"Internal queue failure"}');
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('contacts import process failed');
        $bridge->syncCampaignContacts($campaign, 99, [['email' => 'first@example.test']]);
    }

    public function testSendTransactionalEmailUsesExpectedPayloadAndParams(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options): MockResponse {
            $this->assertSame('POST', $method);
            $this->assertSame('/v3/smtp/email', (string) parse_url($url, PHP_URL_PATH));

            $requestBody = $this->decodeJsonRequestBody($options);
            $this->assertSame([
                'sender' => [
                    'email' => 'brevo@citipo.com',
                    'name' => 'Citipo',
                ],
                'to' => [['email' => 'contact@example.test']],
                'subject' => 'Automation subject',
                'htmlContent' => '<p>Hello</p>',
                'replyTo' => [
                    'email' => 'reply@example.test',
                    'name' => 'Reply Name',
                ],
                'params' => [
                    '-contact-firstname-' => 'Apolline',
                    '-form-answer-1-' => 'Yes',
                ],
            ], $requestBody);

            return new MockResponse('', ['http_code' => 201]);
        });

        $bridge = $this->createBridge($httpClient);
        $bridge->sendTransactionalEmail(
            apiKey: 'test-api-key',
            fromEmail: 'brevo@citipo.com',
            fromName: 'Citipo',
            toEmail: 'contact@example.test',
            subject: 'Automation subject',
            htmlContent: '<p>Hello</p>',
            replyToEmail: 'reply@example.test',
            replyToName: 'Reply Name',
            customVariables: [
                '-contact-firstname-' => 'Apolline',
                '-form-answer-1-' => 'Yes',
            ],
        );
    }

    public function testSendTransactionalEmailBypassesLocalRateLimiter(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);
        $clock = new MockClock('2026-02-12 10:00:00', 'UTC');

        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('GET' === $method && '/v3/contacts/folders' === $path) {
                return new MockResponse('{"folders":[{"id":42}]}');
            }

            if ('POST' === $method && '/v3/contacts/lists' === $path) {
                return new MockResponse('{"id":99}');
            }

            if ('POST' === $method && '/v3/smtp/email' === $path) {
                return new MockResponse('', ['http_code' => 201]);
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $sendLimiter = $this->createLimiterFactory('send', 2, '1 hour');
        $bridge = $this->createBridge($httpClient, $sendLimiter, null, $clock);

        $bridge->createCampaignList($campaign); // Consume all available local send limiter tokens.
        $beforeTransactionalSend = (float) $clock->now()->format('U.u');

        $bridge->sendTransactionalEmail(
            apiKey: 'test-api-key',
            fromEmail: 'brevo@citipo.com',
            fromName: 'Citipo',
            toEmail: 'contact@example.test',
            subject: 'Automation subject',
            htmlContent: '<p>Hello</p>',
        );

        $this->assertEqualsWithDelta($beforeTransactionalSend, (float) $clock->now()->format('U.u'), 0.0001);
    }

    public function testIsEmailCampaignSentReturnsTrueWhenStatusIsSent(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);

        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            $this->assertSame('GET', $method);
            $this->assertSame('/v3/emailCampaigns/201', (string) parse_url($url, PHP_URL_PATH));

            return new MockResponse('{"id":201,"status":"sent"}');
        });

        $bridge = $this->createBridge($httpClient);

        $this->assertTrue($bridge->isEmailCampaignSent($campaign, '201'));
    }

    public function testIsEmailCampaignSentReturnsFalseWhenStatusIsNotSent(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);

        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            $this->assertSame('GET', $method);
            $this->assertSame('/v3/emailCampaigns/201', (string) parse_url($url, PHP_URL_PATH));

            return new MockResponse('{"id":201,"status":"draft"}');
        });

        $bridge = $this->createBridge($httpClient);

        $this->assertFalse($bridge->isEmailCampaignSent($campaign, '201'));
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

    public function testExportEmailCampaignRecipientsRequestsAndPollsProcessUntilCompleted(): void
    {
        $processPolls = 0;
        $clock = new MockClock('2026-02-12 10:00:00', 'UTC');
        $beforeWait = (float) $clock->now()->format('U.u');

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use (&$processPolls): MockResponse {
            if ('POST' === $method && 'https://api.brevo.com/v3/emailCampaigns/42/exportRecipients' === $url) {
                $requestBody = $this->decodeJsonRequestBody($options);
                $this->assertSame('all', $requestBody['recipientsType']);

                return new MockResponse('{"processId":78}', ['http_code' => 202]);
            }

            if ('GET' === $method && 'https://api.brevo.com/v3/processes/78' === $url) {
                ++$processPolls;

                if (1 === $processPolls) {
                    return new MockResponse('{"id":78,"status":"queued"}');
                }

                if (2 === $processPolls) {
                    return new MockResponse('{"id":78,"status":"processing"}');
                }

                return new MockResponse('{"id":78,"status":"completed","export_url":"https://files.example.test/campaign-report.csv"}');
            }

            if ('GET' === $method && 'https://files.example.test/campaign-report.csv' === $url) {
                return new MockResponse("email,opens,clicks\njohn@example.test,2,1\n");
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient, null, null, $clock);

        $this->assertSame(
            "email,opens,clicks\njohn@example.test,2,1\n",
            $bridge->exportEmailCampaignRecipients('test-api-key', '42'),
        );
        $this->assertEqualsWithDelta($beforeWait + 2.0, (float) $clock->now()->format('U.u'), 0.0001);
    }

    public function testExportEmailCampaignRecipientsThrowsWhenProcessFails(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            if ('POST' === $method && 'https://api.brevo.com/v3/emailCampaigns/42/exportRecipients' === $url) {
                return new MockResponse('{"processId":78}', ['http_code' => 202]);
            }

            if ('GET' === $method && 'https://api.brevo.com/v3/processes/78' === $url) {
                return new MockResponse('{"id":78,"status":"failed","error":"Processing timeout exceeded after 30 minutes"}');
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('campaign export process failed');
        $bridge->exportEmailCampaignRecipients('test-api-key', '42');
    }

    public function testRequestRetriesOnceOn429UsingResetHeader(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);
        $clock = new MockClock('2026-02-12 10:00:00', 'UTC');
        $beforeWait = (float) $clock->now()->format('U.u');

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
                return new MockResponse('{"processId":78}', ['http_code' => 202]);
            }

            if ('GET' === $method && '/v3/processes/78' === $path) {
                return new MockResponse('{"id":78,"status":"completed"}');
            }

            if ('POST' === $method && '/v3/emailCampaigns' === $path) {
                return new MockResponse('{"id":201}');
            }

            if ('POST' === $method && '/v3/emailCampaigns/201/sendNow' === $path) {
                return new MockResponse('', ['http_code' => 204]);
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });

        $bridge = $this->createBridge($httpClient, null, null, $clock);
        $bridge->sendEmailCampaign($campaign, '<p>Hello</p>', [['email' => 'first@example.test']]);

        $this->assertEqualsWithDelta($beforeWait + 16.0, (float) $clock->now()->format('U.u'), 0.0001);
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

    public function testLocalRateLimiterWaitsBeforeRetryingRequests(): void
    {
        $campaign = $this->createCampaignMock(emailThrottlingPerHour: null);
        $clock = new MockClock('2026-02-12 10:00:00', 'UTC');
        $beforeWait = (float) $clock->now()->format('U.u');

        $httpClient = new MockHttpClient(function (string $method, string $url): MockResponse {
            $path = (string) parse_url($url, PHP_URL_PATH);

            if ('GET' === $method && '/v3/contacts/folders' === $path) {
                return new MockResponse('{"folders":[{"id":42}]}');
            }

            if ('POST' === $method && '/v3/contacts/lists' === $path) {
                return new MockResponse('{"id":99}');
            }

            if ('POST' === $method && '/v3/contacts/import' === $path) {
                return new MockResponse('{"processId":78}', ['http_code' => 202]);
            }

            if ('GET' === $method && '/v3/processes/78' === $path) {
                return new MockResponse('{"id":78,"status":"completed"}');
            }

            if ('POST' === $method && '/v3/emailCampaigns' === $path) {
                return new MockResponse('{"id":201}');
            }

            if ('POST' === $method && '/v3/emailCampaigns/201/sendNow' === $path) {
                return new MockResponse('', ['http_code' => 204]);
            }

            $this->fail('Unexpected request: '.$method.' '.$url);
        });
        $sendLimiter = $this->createLimiterFactory('send', 4, '1 hour');
        $statsLimiter = $this->createLimiterFactory('stats', 50);
        $bridge = $this->createBridge($httpClient, $sendLimiter, $statsLimiter, $clock);

        $this->assertSame('201', $bridge->sendEmailCampaign($campaign, '<p>Hello</p>', [['email' => 'first@example.test']]));
        $this->assertGreaterThan($beforeWait, (float) $clock->now()->format('U.u'));
    }

    private function createBridge(
        MockHttpClient $httpClient,
        ?RateLimiterFactory $sendCampaignRateLimiter = null,
        ?RateLimiterFactory $campaignStatsRateLimiter = null,
        ?MockClock $clock = null,
    ): TestableBrevo {
        return new TestableBrevo(
            new NullLogger(),
            $httpClient,
            $sendCampaignRateLimiter ?? $this->createLimiterFactory('send', 50),
            $campaignStatsRateLimiter ?? $this->createLimiterFactory('stats', 50),
            'openaction',
            $clock ?? new MockClock('2026-02-12 10:00:00', 'UTC'),
        );
    }

    private function createLimiterFactory(string $id, int $limit, string $interval = '1 hour'): RateLimiterFactory
    {
        return new RateLimiterFactory([
            'id' => $id,
            'policy' => 'sliding_window',
            'limit' => $limit,
            'interval' => $interval,
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
