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
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Brevo implements BrevoInterface
{
    private const CONTACTS_CHUNK_SIZE = 500;
    private const EXPORT_MAX_ATTEMPTS = 10;
    private const EXPORT_WAIT_SECONDS = 1;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly HttpClientInterface $httpClient,
        private readonly string $namespace,
    ) {
    }

    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string
    {
        $organization = $campaign->getProject()->getOrganization();
        $config = $this->createConfiguration($organization->getBrevoApiKey() ?? '');
        $contactsApi = $this->createContactsApi($config);

        try {
            $listId = $this->createCampaignList($contactsApi, $campaign);

            $this->syncContacts(
                contactsApi: $contactsApi,
                listId: $listId,
                contacts: $contacts,
            );

            $brevoCampaign = $this->buildCampaignBody($campaign, $htmlContent);
            $brevoCampaign->setRecipients((new CreateEmailCampaignRecipients())->setListIds([$listId]));

            $emailCampaignsApi = $this->createEmailCampaignsApi($config);
            $createdCampaign = $emailCampaignsApi->createEmailCampaign($brevoCampaign);
            $emailCampaignsApi->sendEmailCampaignNow($createdCampaign->getId());

            return (string) $createdCampaign->getId();
        } catch (ApiException $exception) {
            $this->handleApiException($exception);
        }

        throw new \RuntimeException('Brevo campaign sending failed.');
    }

    public function getCampaignReport(string $apiKey, string $campaignId, ?string $campaignTag = null): array
    {
        unset($campaignTag);

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

            $report = [];
            foreach ($sent as $email) {
                $report[$email] = [
                    'sent' => true,
                    'opened' => in_array($email, $opened, true),
                    'clicked' => in_array($email, $clicked, true),
                    'bounced' => in_array($email, $bounced, true),
                ];

                if ($report[$email]['clicked']) {
                    $report[$email]['opened'] = true;
                }
            }

            foreach ($bounced as $email) {
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

        throw new \RuntimeException('Brevo campaign report retrieval failed.');
    }

    protected function buildCampaignBody(EmailingCampaign $campaign, string $htmlContent): CreateEmailCampaign
    {
        $organization = $campaign->getProject()->getOrganization();

        $sender = (new CreateEmailCampaignSender())
            ->setName($campaign->getFromName() ?: $organization->getName())
            ->setEmail($organization->getBrevoSenderEmail());

        $body = (new CreateEmailCampaign())
            ->setTag($this->getCampaignListName($campaign))
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

    protected function createCampaignList(ContactsApi $contactsApi, EmailingCampaign $campaign): int
    {
        $list = (new CreateList())
            ->setName($this->getCampaignListName($campaign))
            ->setFolderId($this->resolveCampaignFolderId($contactsApi));

        return (int) $contactsApi->createList($list)->getId();
    }

    protected function resolveCampaignFolderId(ContactsApi $contactsApi): int
    {
        $folders = $contactsApi->getFolders('1', '0', 'asc')->getFolders() ?? [];

        foreach ($folders as $folder) {
            $folderId = $this->extractIntProperty($folder, 'id');

            if ($folderId) {
                return $folderId;
            }
        }

        throw new \RuntimeException('Brevo error: no contact folder available. Please create at least one contacts folder in Brevo.');
    }

    protected function extractIntProperty(mixed $value, string $key): ?int
    {
        if (is_array($value) && isset($value[$key]) && is_numeric($value[$key])) {
            return (int) $value[$key];
        }

        if (is_object($value) && isset($value->{$key}) && is_numeric($value->{$key})) {
            return (int) $value->{$key};
        }

        return null;
    }

    protected function getCampaignListName(EmailingCampaign $campaign): string
    {
        return sprintf('%s-campaign-%d', $this->namespace, $campaign->getId());
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
        $value = trim((string) $value);

        return '' === $value ? null : $value;
    }

    protected function fetchExportedEmails(EmailCampaignsApi $emailCampaignsApi, ProcessApi $processApi, string $campaignId, string $recipientsType): array
    {
        $export = (new EmailExportRecipients())->setRecipientsType($recipientsType);
        $process = $emailCampaignsApi->emailExportRecipients((int) $campaignId, $export);

        $exportUrl = $this->waitForExportUrl($processApi, $process->getProcessId());
        $content = $this->httpClient->request('GET', $exportUrl)->getContent();

        return $this->parseExportedEmails($content);
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
        $emails = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        if (!$lines) {
            return [];
        }

        $delimiter = str_contains($lines[0], ';') ? ';' : ',';

        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }

            $columns = str_getcsv($line, $delimiter);
            $email = strtolower(trim($columns[0] ?? ''));

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[$email] = true;
            }
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

    protected function handleApiException(ApiException $exception): void
    {
        $this->logger->error('Brevo API error: '.$exception->getMessage(), [
            'exception' => $exception,
            'response' => $exception->getResponseBody(),
            'response_headers' => $exception->getResponseHeaders(),
        ]);

        throw new \RuntimeException('Brevo error: '.$exception->getMessage());
    }
}
