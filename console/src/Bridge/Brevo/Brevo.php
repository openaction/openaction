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
    ) {
    }

    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string
    {
        $organization = $campaign->getProject()->getOrganization();
        $config = $this->createConfiguration($organization->getBrevoApiKey() ?? '');

        try {
            $this->syncContacts(
                contactsApi: $this->createContactsApi($config),
                listId: (int) $organization->getBrevoListId(),
                contacts: $contacts,
            );

            $campaignTag = $organization->getBrevoCampaignTag() ?: 'citipo-campaign-'.$campaign->getId();
            $brevoCampaign = $this->buildCampaignBody($campaign, $htmlContent, $campaignTag);
            $brevoCampaign->setRecipients((new CreateEmailCampaignRecipients())->setListIds([(int) $organization->getBrevoListId()]));

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

    protected function buildCampaignBody(EmailingCampaign $campaign, string $htmlContent, string $tag): CreateEmailCampaign
    {
        $organization = $campaign->getProject()->getOrganization();

        $sender = (new CreateEmailCampaignSender())
            ->setName($campaign->getFromName() ?: $organization->getName());

        if ($organization->getBrevoSenderId()) {
            $sender->setId($organization->getBrevoSenderId());
        } elseif ($organization->getBrevoSenderEmail()) {
            $sender->setEmail($organization->getBrevoSenderEmail());
        }

        $body = (new CreateEmailCampaign())
            ->setTag($tag)
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
