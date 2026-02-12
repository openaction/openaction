<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;
use Psr\Log\LoggerInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Brevo implements BrevoInterface
{
    private const CONTACTS_CHUNK_SIZE = 500;
    private const CAMPAIGNS_PER_HOUR = 4;
    private const THROTTLED_CAMPAIGN_INTERVAL_MINUTES = 15;
    private const CAMPAIGNS_STATS_PAGE_SIZE = 100;
    private const API_BASE_URL = 'https://api.brevo.com/v3';
    private const MAX_RATE_LIMIT_ATTEMPTS = 2;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly HttpClientInterface $httpClient,
        private readonly RateLimiterFactory $sendCampaignRateLimiter,
        private readonly RateLimiterFactory $campaignStatsRateLimiter,
        private readonly string $namespace,
    ) {
    }

    /**
     * @return string[]
     */
    public function sendEmailCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): array
    {
        $organization = $campaign->getProject()->getOrganization();
        $apiKey = (string) ($organization->getBrevoApiKey() ?? '');
        $throttlingPerHour = $organization->getEmailThrottlingPerHour();
        $listCapacity = $this->computeListCapacity($throttlingPerHour);
        $isThrottled = null !== $listCapacity;
        $scheduledBaseAt = $this->getCurrentUtcDateTime();
        $contactsChunks = $isThrottled ? array_chunk($contacts, $listCapacity) : [$contacts];

        if (!$contactsChunks) {
            $contactsChunks = [[]];
        }

        $createdCampaignIds = [];

        foreach ($contactsChunks as $chunkIndex => $contactsChunk) {
            $listId = $this->createCampaignList(
                $apiKey,
                $campaign,
                $isThrottled ? $chunkIndex + 1 : null,
            );

            $this->syncContacts(
                apiKey: $apiKey,
                listId: $listId,
                contacts: $contactsChunk,
            );

            $brevoCampaignPayload = $this->buildCampaignPayload($campaign, $htmlContent);
            $brevoCampaignPayload['recipients'] = ['listIds' => [$listId]];

            if ($isThrottled) {
                $scheduledAt = $scheduledBaseAt->modify(sprintf('+%d minutes', $chunkIndex * self::THROTTLED_CAMPAIGN_INTERVAL_MINUTES));
                $brevoCampaignPayload['scheduledAt'] = $this->formatScheduledAt($scheduledAt);
            }

            $createdCampaign = $this->decodeJsonResponse(
                $this->requestBrevo(
                    method: 'POST',
                    endpoint: '/emailCampaigns',
                    apiKey: $apiKey,
                    options: ['json' => $brevoCampaignPayload],
                    limiter: $this->sendCampaignRateLimiter,
                    limiterContext: 'campaign_send',
                ),
                operation: 'create email campaign',
            );

            $createdCampaignId = trim((string) ($createdCampaign['id'] ?? ''));
            if ('' === $createdCampaignId) {
                throw new \RuntimeException('Brevo error: campaign could not be created.');
            }

            $createdCampaignIds[] = $createdCampaignId;

            if (!$isThrottled) {
                $this->requestBrevo(
                    method: 'POST',
                    endpoint: '/emailCampaigns/'.$createdCampaignId.'/sendNow',
                    apiKey: $apiKey,
                    options: ['json' => new \stdClass()],
                    limiter: $this->sendCampaignRateLimiter,
                    limiterContext: 'campaign_send',
                );
            }
        }

        return $createdCampaignIds;
    }

    public function getEmailCampaignsStats(string $apiKey): array
    {
        $campaignsStats = [];
        $offset = 0;

        while (true) {
            $response = $this->requestBrevo(
                method: 'GET',
                endpoint: '/emailCampaigns',
                apiKey: $apiKey,
                options: [
                    'query' => [
                        'status' => 'sent',
                        'statistics' => 'globalStats',
                        'limit' => self::CAMPAIGNS_STATS_PAGE_SIZE,
                        'excludeHtmlContent' => 'true',
                        'offset' => $offset,
                    ],
                ],
                limiter: $this->campaignStatsRateLimiter,
                limiterContext: 'campaign_stats',
            );
            $payload = $this->decodeJsonResponse($response, 'fetch email campaigns stats');
            $campaigns = $payload['campaigns'] ?? [];

            if (!is_array($campaigns)) {
                break;
            }

            foreach ($campaigns as $campaign) {
                if (!is_array($campaign)) {
                    continue;
                }

                $campaignId = trim((string) ($campaign['id'] ?? ''));

                if ('' === $campaignId) {
                    continue;
                }

                $globalStats = $campaign['statistics']['globalStats'] ?? [];
                $campaignsStats[$campaignId] = is_array($globalStats) ? $globalStats : [];
            }

            $totalCount = is_numeric($payload['count'] ?? null)
                ? (int) $payload['count']
                : $offset + count($campaigns);

            $offset += self::CAMPAIGNS_STATS_PAGE_SIZE;

            if (0 === count($campaigns) || $offset >= $totalCount) {
                break;
            }
        }

        return $campaignsStats;
    }

    public function getEmailCampaignReport(string $apiKey, string $campaignId): array
    {
        return [];
    }

    protected function buildCampaignPayload(EmailingCampaign $campaign, string $htmlContent): array
    {
        $organization = $campaign->getProject()->getOrganization();

        $payload = [
            'name' => $campaign->getSubject(),
            'subject' => $campaign->getSubject(),
            'sender' => [
                'name' => $campaign->getFromName() ?: $organization->getName(),
                'email' => $organization->getBrevoSenderEmail(),
            ],
            'htmlContent' => $htmlContent,
            'replyTo' => $campaign->getReplyToEmail() ?: $campaign->getFullFromEmail(),
        ];

        if ($campaign->getPreview()) {
            $payload['previewText'] = $campaign->getPreview();
        }

        return $payload;
    }

    protected function syncContacts(string $apiKey, int $listId, array $contacts): void
    {
        foreach (array_chunk($contacts, self::CONTACTS_CHUNK_SIZE) as $chunk) {
            $jsonBody = [];

            foreach ($chunk as $contact) {
                if (empty($contact['email'])) {
                    continue;
                }

                $body = ['email' => strtolower((string) $contact['email'])];
                $attributes = $this->buildContactAttributes($contact);

                if ($attributes) {
                    $body['attributes'] = $attributes;
                }

                $jsonBody[] = $body;
            }

            if (!$jsonBody) {
                continue;
            }

            $this->requestBrevo(
                method: 'POST',
                endpoint: '/contacts/import',
                apiKey: $apiKey,
                options: [
                    'json' => [
                        'listIds' => [$listId],
                        'jsonBody' => $jsonBody,
                        'updateExistingContacts' => true,
                    ],
                ],
                limiter: $this->sendCampaignRateLimiter,
                limiterContext: 'campaign_send',
            );
        }
    }

    protected function createCampaignList(string $apiKey, EmailingCampaign $campaign, ?int $chunkIndex = null): int
    {
        $listName = sprintf('%s-campaign-%d', $this->namespace, $campaign->getId());

        if (null !== $chunkIndex) {
            $listName .= '-'.$chunkIndex;
        }

        $payload = $this->decodeJsonResponse(
            $this->requestBrevo(
                method: 'POST',
                endpoint: '/contacts/lists',
                apiKey: $apiKey,
                options: [
                    'json' => [
                        'name' => $listName,
                        'folderId' => $this->resolveCampaignFolderId($apiKey),
                    ],
                ],
                limiter: $this->sendCampaignRateLimiter,
                limiterContext: 'campaign_send',
            ),
            operation: 'create contacts list',
        );

        if (!is_numeric($payload['id'] ?? null)) {
            throw new \RuntimeException('Brevo error: list could not be created.');
        }

        return (int) $payload['id'];
    }

    protected function computeListCapacity(?int $throttlingPerHour): ?int
    {
        if (!$throttlingPerHour || $throttlingPerHour <= 0) {
            return null;
        }

        return max((int) floor($throttlingPerHour / self::CAMPAIGNS_PER_HOUR), 1);
    }

    protected function getCurrentUtcDateTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    protected function formatScheduledAt(\DateTimeImmutable $scheduledAt): string
    {
        return $scheduledAt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.v\Z');
    }

    protected function resolveCampaignFolderId(string $apiKey): int
    {
        $payload = $this->decodeJsonResponse(
            $this->requestBrevo(
                method: 'GET',
                endpoint: '/contacts/folders',
                apiKey: $apiKey,
                options: [
                    'query' => [
                        'limit' => 1,
                        'offset' => 0,
                        'sort' => 'asc',
                    ],
                ],
                limiter: $this->sendCampaignRateLimiter,
                limiterContext: 'campaign_send',
            ),
            operation: 'resolve contacts folder',
        );
        $folders = $payload['folders'] ?? [];

        foreach ($folders as $folder) {
            $folderId = is_array($folder) ? ($folder['id'] ?? null) : null;

            if (is_numeric($folderId)) {
                return (int) $folderId;
            }
        }

        throw new \RuntimeException('Brevo error: no contact folder available. Please create at least one contacts folder in Brevo.');
    }

    protected function buildContactAttributes(array $contact): array
    {
        return array_filter([
            'PHONE' => $this->normalizeAttributeValue($contact['phone'] ?? null),
            'FORMAL_TITLE' => $this->normalizeAttributeValue($contact['formalTitle'] ?? null),
            'FIRST_NAME' => $this->normalizeAttributeValue($contact['firstName'] ?? null),
            'LAST_NAME' => $this->normalizeAttributeValue($contact['lastName'] ?? null),
            'FULL_NAME' => $this->normalizeAttributeValue($contact['fullName'] ?? null),
            'GENDER' => $this->normalizeAttributeValue($contact['gender'] ?? null),
            'NATIONALITY' => $this->normalizeAttributeValue($contact['nationality'] ?? null),
            'COMPANY' => $this->normalizeAttributeValue($contact['company'] ?? null),
            'JOB_TITLE' => $this->normalizeAttributeValue($contact['jobTitle'] ?? null),
            'ADDRESS_LINE_1' => $this->normalizeAttributeValue($contact['addressLine1'] ?? null),
            'ADDRESS_LINE_2' => $this->normalizeAttributeValue($contact['addressLine2'] ?? null),
            'POSTAL_CODE' => $this->normalizeAttributeValue($contact['postalCode'] ?? null),
            'CITY' => $this->normalizeAttributeValue($contact['city'] ?? null),
            'COUNTRY' => $this->normalizeAttributeValue($contact['country'] ?? null),
        ], static fn (?string $value): bool => null !== $value);
    }

    protected function normalizeAttributeValue(mixed $value): ?string
    {
        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        } elseif ($value instanceof \UnitEnum) {
            $value = $value->name;
        } elseif (is_object($value) && !method_exists($value, '__toString')) {
            return null;
        }

        if (!is_scalar($value) && !$value instanceof \Stringable) {
            return null;
        }

        $value = trim((string) $value);

        return '' === $value ? null : $value;
    }

    protected function waitForSeconds(int $seconds): void
    {
        sleep(max(0, $seconds));
    }

    private function requestBrevo(
        string $method,
        string $endpoint,
        string $apiKey,
        array $options,
        RateLimiterFactory $limiter,
        string $limiterContext,
    ): ResponseInterface {
        $requestOptions = array_replace_recursive([
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $apiKey,
            ],
        ], $options);

        for ($attempt = 1; $attempt <= self::MAX_RATE_LIMIT_ATTEMPTS; ++$attempt) {
            $this->consumeRateLimit(
                limiter: $limiter,
                limiterContext: $limiterContext,
                apiKey: $apiKey,
                method: $method,
                endpoint: $endpoint,
            );

            try {
                $response = $this->httpClient->request(
                    $method,
                    self::API_BASE_URL.$endpoint,
                    $requestOptions,
                );
            } catch (TransportExceptionInterface $exception) {
                $this->logger->error('Brevo HTTP transport failed', [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'exception' => $exception,
                ]);

                throw new \RuntimeException('Brevo error: '.$exception->getMessage(), previous: $exception);
            }

            $statusCode = $response->getStatusCode();

            if (429 === $statusCode) {
                $rateLimitResetHeader = $response->getHeaders(false)['x-sib-ratelimit-reset'][0] ?? null;
                $waitSeconds = $this->extractWaitSecondsFromRateLimitResetHeader($rateLimitResetHeader);

                if ($attempt < self::MAX_RATE_LIMIT_ATTEMPTS) {
                    $this->logger->warning('Brevo API rate limit reached, retrying once', [
                        'method' => $method,
                        'endpoint' => $endpoint,
                        'status_code' => $statusCode,
                        'attempt' => $attempt,
                        'wait_seconds' => $waitSeconds,
                        'x_sib_ratelimit_reset' => $rateLimitResetHeader,
                    ]);

                    $this->waitForSeconds($waitSeconds);

                    continue;
                }

                $this->logger->error('Brevo API rate limit reached after one retry', [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'status_code' => $statusCode,
                    'attempt' => $attempt,
                    'wait_seconds' => $waitSeconds,
                    'x_sib_ratelimit_reset' => $rateLimitResetHeader,
                    'response_headers' => $response->getHeaders(false),
                    'response_body' => $response->getContent(false),
                ]);

                throw new \RuntimeException(sprintf('Brevo error: rate limit reached for %s %s after one retry.', $method, $endpoint));
            }

            if ($statusCode >= 400) {
                $responseBody = $response->getContent(false);
                $decodedResponse = json_decode($responseBody, true);
                $brevoErrorMessage = is_array($decodedResponse)
                    ? trim((string) ($decodedResponse['message'] ?? $decodedResponse['code'] ?? ''))
                    : '';

                if ('' === $brevoErrorMessage) {
                    $brevoErrorMessage = 'Unexpected Brevo API error';
                }

                $this->logger->error('Brevo API request failed', [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'status_code' => $statusCode,
                    'response_headers' => $response->getHeaders(false),
                    'response_body' => $responseBody,
                ]);

                throw new \RuntimeException(sprintf('Brevo error: %s (HTTP %d).', $brevoErrorMessage, $statusCode));
            }

            return $response;
        }

        throw new \RuntimeException(sprintf('Brevo error: %s %s failed.', $method, $endpoint));
    }

    private function decodeJsonResponse(ResponseInterface $response, string $operation): array
    {
        $content = trim($response->getContent(false));

        if ('' === $content) {
            return [];
        }

        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            $this->logger->error('Brevo API returned an invalid JSON payload', [
                'operation' => $operation,
                'response_body' => $content,
            ]);

            throw new \RuntimeException('Brevo error: invalid API response while trying to '.$operation.'.');
        }

        return $decoded;
    }

    private function consumeRateLimit(
        RateLimiterFactory $limiter,
        string $limiterContext,
        string $apiKey,
        string $method,
        string $endpoint,
    ): void {
        $rateLimit = $limiter->create($this->createLimiterKey($apiKey, $limiterContext))->consume();

        if ($rateLimit->isAccepted()) {
            return;
        }

        $retryAfter = $rateLimit->getRetryAfter();
        $waitSeconds = max(1, $retryAfter->getTimestamp() - time());

        $this->logger->error('Brevo local rate limiter blocked request', [
            'limiter_context' => $limiterContext,
            'method' => $method,
            'endpoint' => $endpoint,
            'retry_after' => $retryAfter->format(\DateTimeInterface::ATOM),
            'wait_seconds' => $waitSeconds,
            'remaining_tokens' => $rateLimit->getRemainingTokens(),
            'limit' => $rateLimit->getLimit(),
        ]);

        throw new \RuntimeException(sprintf('Brevo error: local rate limiter reached for %s requests. Retry after %d seconds.', $limiterContext, $waitSeconds));
    }

    private function createLimiterKey(string $apiKey, string $limiterContext): string
    {
        return sprintf('%s_%s', $limiterContext, hash('sha256', $apiKey));
    }

    private function extractWaitSecondsFromRateLimitResetHeader(mixed $rateLimitResetHeader): int
    {
        if (!is_numeric($rateLimitResetHeader)) {
            return 1;
        }

        return max(0, (int) $rateLimitResetHeader) + 1;
    }
}
