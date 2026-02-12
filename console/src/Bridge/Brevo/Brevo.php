<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;
use Brevo\Client\Api\ContactsApi;
use Brevo\Client\Api\EmailCampaignsApi;
use Brevo\Client\Api\ProcessApi;
use Brevo\Client\ApiException;
use Brevo\Client\Configuration;
use Brevo\Client\Model\CreateEmailCampaign;
use Brevo\Client\Model\CreateEmailCampaignRecipients;
use Brevo\Client\Model\CreateEmailCampaignSender;
use Brevo\Client\Model\CreateList;
use Brevo\Client\Model\EmailExportRecipients;
use Brevo\Client\Model\RequestContactImport;
use Brevo\Client\Model\RequestContactImportJsonBody;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Brevo implements BrevoInterface
{
    private const CONTACTS_CHUNK_SIZE = 500;
    private const EXPORT_MAX_ATTEMPTS = 10;
    private const EXPORT_WAIT_SECONDS = 1;
    private const CAMPAIGNS_PER_HOUR = 4;
    private const THROTTLED_CAMPAIGN_INTERVAL_MINUTES = 15;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly HttpClientInterface $httpClient,
        private readonly string $namespace,
    ) {
    }

    /**
     * @return string[]
     */
    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): array
    {
        $organization = $campaign->getProject()->getOrganization();
        $config = $this->createConfiguration($organization->getBrevoApiKey() ?? '');
        $contactsApi = $this->createContactsApi($config);
        $emailCampaignsApi = $this->createEmailCampaignsApi($config);
        $throttlingPerHour = $organization->getEmailThrottlingPerHour();
        $listCapacity = $this->computeListCapacity($throttlingPerHour);
        $isThrottled = null !== $listCapacity;
        $scheduledBaseAt = $this->getCurrentUtcDateTime();
        $contactsChunks = $isThrottled ? array_chunk($contacts, $listCapacity) : [$contacts];

        if (!$contactsChunks) {
            $contactsChunks = [[]];
        }

        try {
            $createdCampaignIds = [];

            foreach ($contactsChunks as $chunkIndex => $contactsChunk) {
                $listId = $this->createCampaignList(
                    $contactsApi,
                    $campaign,
                    $isThrottled ? $chunkIndex + 1 : null,
                );

                $this->syncContacts(
                    contactsApi: $contactsApi,
                    listId: $listId,
                    contacts: $contactsChunk,
                );

                $brevoCampaign = $this->buildCampaignBody($campaign, $htmlContent);
                $brevoCampaign->setRecipients((new CreateEmailCampaignRecipients())->setListIds([$listId]));

                if ($isThrottled) {
                    $scheduledAt = $scheduledBaseAt->modify(sprintf('+%d minutes', $chunkIndex * self::THROTTLED_CAMPAIGN_INTERVAL_MINUTES));
                    $brevoCampaign->setScheduledAt($this->formatScheduledAt($scheduledAt));
                }

                $createdCampaign = $emailCampaignsApi->createEmailCampaign($brevoCampaign);
                $createdCampaignIds[] = (string) $createdCampaign->getId();

                if (!$isThrottled) {
                    $emailCampaignsApi->sendEmailCampaignNow($createdCampaign->getId());
                }
            }

            return $createdCampaignIds;
        } catch (ApiException $exception) {
            $this->handleApiException($exception);
        }
    }

    public function getCampaignReport(string $apiKey, string $campaignId): array
    {
        $config = $this->createConfiguration($apiKey);
        $emailCampaignsApi = $this->createEmailCampaignsApi($config);
        $processApi = $this->createProcessApi($config);

        try {
            $sent = $this->fetchExportedEmails($emailCampaignsApi, $processApi, $campaignId, EmailExportRecipients::RECIPIENTS_TYPE_ALL);
            $opened = $this->fetchExportedEmails($emailCampaignsApi, $processApi, $campaignId, EmailExportRecipients::RECIPIENTS_TYPE_OPENERS);
            $clicked = $this->fetchExportedEmails($emailCampaignsApi, $processApi, $campaignId, EmailExportRecipients::RECIPIENTS_TYPE_CLICKERS);
            $bounced = array_merge(
                $this->fetchExportedEmails($emailCampaignsApi, $processApi, $campaignId, EmailExportRecipients::RECIPIENTS_TYPE_SOFT_BOUNCES),
                $this->fetchExportedEmails($emailCampaignsApi, $processApi, $campaignId, EmailExportRecipients::RECIPIENTS_TYPE_HARD_BOUNCES),
            );
            $openedLookup = array_fill_keys($opened, true);
            $clickedLookup = array_fill_keys($clicked, true);
            $bouncedLookup = array_fill_keys($bounced, true);

            $report = [];
            foreach ($sent as $email) {
                $isClicked = isset($clickedLookup[$email]);

                $report[$email] = [
                    'sent' => true,
                    'opened' => $isClicked || isset($openedLookup[$email]),
                    'clicked' => $isClicked,
                    'bounced' => isset($bouncedLookup[$email]),
                ];
            }

            foreach (array_keys($bouncedLookup) as $email) {
                if (!isset($report[$email])) {
                    $report[$email] = [
                        'sent' => false,
                        'opened' => false,
                        'clicked' => false,
                        'bounced' => true,
                    ];
                } else {
                    $report[$email]['bounced'] = true;
                }
            }

            return $report;
        } catch (ApiException $exception) {
            $this->handleApiException($exception);
        }
    }

    protected function buildCampaignBody(EmailingCampaign $campaign, string $htmlContent): CreateEmailCampaign
    {
        $organization = $campaign->getProject()->getOrganization();

        $sender = (new CreateEmailCampaignSender())
            ->setName($campaign->getFromName() ?: $organization->getName())
            ->setEmail($organization->getBrevoSenderEmail());

        $body = (new CreateEmailCampaign())
            ->setName($campaign->getSubject())
            ->setSubject($campaign->getSubject())
            ->setSender($sender)
            ->setHtmlContent($htmlContent)
            ->setReplyTo($campaign->getReplyToEmail() ?: $campaign->getFullFromEmail());

        if ($campaign->getPreview()) {
            $body->setPreviewText($campaign->getPreview());
        }

        return $body;
    }

    protected function syncContacts(ContactsApi $contactsApi, int $listId, array $contacts): void
    {
        foreach (array_chunk($contacts, self::CONTACTS_CHUNK_SIZE) as $chunk) {
            $jsonBody = [];

            foreach ($chunk as $contact) {
                if (empty($contact['email'])) {
                    continue;
                }

                $body = (new RequestContactImportJsonBody())->setEmail(strtolower($contact['email']));
                $attributes = $this->buildContactAttributes($contact);

                if ($attributes) {
                    $body->setAttributes($attributes);
                }

                $jsonBody[] = $body;
            }

            if (!$jsonBody) {
                continue;
            }

            $request = (new RequestContactImport())
                ->setListIds([$listId])
                ->setJsonBody($jsonBody)
                ->setUpdateExistingContacts(true);

            $contactsApi->importContacts($request);
        }
    }

    protected function createCampaignList(ContactsApi $contactsApi, EmailingCampaign $campaign, ?int $chunkIndex = null): int
    {
        $listName = sprintf('%s-campaign-%d', $this->namespace, $campaign->getId());

        if (null !== $chunkIndex) {
            $listName .= '-'.$chunkIndex;
        }

        $list = (new CreateList())
            ->setName($listName)
            ->setFolderId($this->resolveCampaignFolderId($contactsApi));

        return (int) $contactsApi->createList($list)->getId();
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

    protected function resolveCampaignFolderId(ContactsApi $contactsApi): int
    {
        $folders = $contactsApi->getFolders('1', '0', 'asc')->getFolders() ?? [];

        foreach ($folders as $folder) {
            $folderId = null;

            if (is_array($folder)) {
                $folderId = $folder['id'] ?? null;
            } elseif (is_object($folder)) {
                $folderId = $folder->id ?? null;
            }

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

    protected function fetchExportedEmails(EmailCampaignsApi $emailCampaignsApi, ProcessApi $processApi, string $campaignId, string $recipientsType): array
    {
        $process = $emailCampaignsApi->emailExportRecipients(
            (int) $campaignId,
            new EmailExportRecipients()->setRecipientsType($recipientsType),
        );

        $processId = $process->getProcessId();

        if (!$processId) {
            return [];
        }

        $exportUrl = $this->waitForExportUrl($processApi, $processId);
        $csvContent = $this->httpClient->request('GET', $exportUrl)->getContent();

        return $this->parseExportedEmails($csvContent);
    }

    protected function waitForExportUrl(ProcessApi $processApi, int $processId): string
    {
        $attempts = 0;

        while ($attempts < self::EXPORT_MAX_ATTEMPTS) {
            ++$attempts;

            $process = $processApi->getProcess($processId);

            if ('completed' === $process->getStatus() && $process->getExportUrl()) {
                return $process->getExportUrl();
            }

            if ('failed' === $process->getStatus()) {
                break;
            }

            sleep(self::EXPORT_WAIT_SECONDS);
        }

        throw new \RuntimeException('Brevo export did not complete for process '.$processId);
    }

    protected function parseExportedEmails(string $content): array
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'brevo_export_');

        if (false === $tempPath) {
            throw new \RuntimeException('Unable to create temporary file for Brevo export parsing.');
        }

        if (false === file_put_contents($tempPath, $content)) {
            @unlink($tempPath);

            throw new \RuntimeException('Unable to write Brevo export in temporary file.');
        }

        $emails = [];
        $isFirstRow = true;
        $emailColumnIndex = 0;
        $reader = new Reader(new Options(FIELD_DELIMITER: ';'));

        try {
            $reader->open($tempPath);

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $columns = $row->toArray();

                    if ($isFirstRow) {
                        $isFirstRow = false;
                        $headerEmailColumnIndex = null;

                        foreach ($columns as $index => $column) {
                            $normalizedColumn = strtolower(trim((string) $column));

                            if (in_array($normalizedColumn, ['email_id', 'email id', 'email'], true)) {
                                $headerEmailColumnIndex = (int) $index;

                                break;
                            }
                        }

                        if (null !== $headerEmailColumnIndex) {
                            $emailColumnIndex = $headerEmailColumnIndex;

                            continue;
                        }
                    }

                    $email = strtolower(trim((string) ($columns[$emailColumnIndex] ?? '')));

                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emails[$email] = true;
                    }
                }
            }
        } finally {
            $reader->close();
            @unlink($tempPath);
        }

        return array_keys($emails);
    }

    protected function createConfiguration(string $apiKey): Configuration
    {
        return (new Configuration())->setApiKey('api-key', $apiKey);
    }

    protected function createContactsApi(Configuration $config): ContactsApi
    {
        return new ContactsApi(null, $config);
    }

    protected function createEmailCampaignsApi(Configuration $config): EmailCampaignsApi
    {
        return new EmailCampaignsApi(null, $config);
    }

    protected function createProcessApi(Configuration $config): ProcessApi
    {
        return new ProcessApi(null, $config);
    }

    protected function handleApiException(ApiException $exception): never
    {
        $this->logger->error('Brevo API error: '.$exception->getMessage(), [
            'exception' => $exception,
            'response' => $exception->getResponseBody(),
            'response_headers' => $exception->getResponseHeaders(),
        ]);

        throw new \RuntimeException('Brevo error: '.$exception->getMessage());
    }
}
