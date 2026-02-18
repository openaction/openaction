<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

class MockBrevo implements BrevoInterface
{
    public array $campaigns = [];

    public array $transactionalEmails = [];

    public array $lists = [];

    public array $listContacts = [];

    public array $campaignsStats = [];

    public array $reports = [];

    public array $reportExports = [];

    public array $campaignStatsCalls = [];

    public int $campaignReportCalls = 0;

    public int $campaignReportExportCalls = 0;

    public int $createCampaignListCalls = 0;

    public int $syncCampaignContactsCalls = 0;

    public int $createEmailCampaignCalls = 0;

    public int $isEmailCampaignSentCalls = 0;

    public int $sendEmailCampaignNowCalls = 0;

    public int $sendTransactionalEmailCalls = 0;

    public function sendEmailCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string
    {
        $listId = $this->createCampaignList($campaign);
        $this->syncCampaignContacts($campaign, $listId, $contacts);
        $campaignId = $this->createEmailCampaign($campaign, $htmlContent, $listId);
        $this->sendEmailCampaignNow($campaign, $campaignId);

        return $campaignId;
    }

    public function sendTransactionalEmail(
        string $apiKey,
        string $fromEmail,
        ?string $fromName,
        string $toEmail,
        string $subject,
        string $htmlContent,
        ?string $replyToEmail = null,
        ?string $replyToName = null,
        array $customVariables = [],
    ): void {
        ++$this->sendTransactionalEmailCalls;
        $this->transactionalEmails[] = [
            'apiKey' => $apiKey,
            'fromEmail' => $fromEmail,
            'fromName' => $fromName,
            'toEmail' => $toEmail,
            'subject' => $subject,
            'htmlContent' => $htmlContent,
            'replyToEmail' => $replyToEmail,
            'replyToName' => $replyToName,
            'customVariables' => $customVariables,
        ];
    }

    public function createCampaignList(EmailingCampaign $campaign): int
    {
        ++$this->createCampaignListCalls;
        $listId = count($this->lists) + 1;
        $this->lists[$listId] = [
            'campaign' => $campaign,
        ];

        return $listId;
    }

    public function syncCampaignContacts(EmailingCampaign $campaign, int $listId, array $contacts): void
    {
        ++$this->syncCampaignContactsCalls;
        $this->listContacts[$listId] = $contacts;
    }

    public function createEmailCampaign(EmailingCampaign $campaign, string $htmlContent, int $listId): string
    {
        ++$this->createEmailCampaignCalls;
        $id = (string) (count($this->campaigns) + 1);
        $contacts = $this->listContacts[$listId] ?? [];
        $throttlingPerHour = $campaign->getProject()->getOrganization()->getEmailThrottlingPerHour();
        $batchConfiguration = $this->computeBatchConfiguration(
            contactsCount: count(array_filter(
                $contacts,
                static fn (array $contact): bool => !empty($contact['email']),
            )),
            throttlingPerHour: $throttlingPerHour,
        );

        $this->campaigns[$id] = [
            'campaign' => $campaign,
            'html' => $htmlContent,
            'contacts' => $contacts,
            'listId' => $listId,
            'status' => 'draft',
            'scheduledAt' => $batchConfiguration ? new \DateTimeImmutable('now') : null,
            'batching' => $batchConfiguration,
        ];

        return $id;
    }

    public function isEmailCampaignSent(EmailingCampaign $campaign, string $campaignId): bool
    {
        ++$this->isEmailCampaignSentCalls;

        return 'sent' === ($this->campaigns[$campaignId]['status'] ?? null);
    }

    public function sendEmailCampaignNow(EmailingCampaign $campaign, string $campaignId): void
    {
        ++$this->sendEmailCampaignNowCalls;

        if (!isset($this->campaigns[$campaignId])) {
            throw new \RuntimeException(sprintf('Brevo mock campaign "%s" not found.', $campaignId));
        }

        $this->campaigns[$campaignId]['status'] = 'sent';
    }

    public function getEmailCampaignsStats(
        string $apiKey,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
    ): array {
        $this->campaignStatsCalls[] = [
            'apiKey' => $apiKey,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return $this->campaignsStats;
    }

    public function getEmailCampaignReport(string $apiKey, string $campaignId): array
    {
        ++$this->campaignReportCalls;

        return $this->reports[$campaignId] ?? [];
    }

    public function exportEmailCampaignRecipients(string $apiKey, string $campaignId): string
    {
        ++$this->campaignReportExportCalls;

        return $this->reportExports[$campaignId] ?? '';
    }

    private function computeBatchConfiguration(int $contactsCount, ?int $throttlingPerHour): ?array
    {
        if (!$throttlingPerHour || $throttlingPerHour <= 0 || $contactsCount < $throttlingPerHour) {
            return null;
        }

        if ($contactsCount <= 10 * $throttlingPerHour) {
            return [
                'batchSize' => $throttlingPerHour,
                'batchesCount' => max((int) ceil($contactsCount / $throttlingPerHour), 1),
                'intervalMinutes' => 30,
            ];
        }

        return [
            'batchSize' => (int) ceil($contactsCount / 10),
            'batchesCount' => 10,
            'intervalMinutes' => 30,
        ];
    }
}
