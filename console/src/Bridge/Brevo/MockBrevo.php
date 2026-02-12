<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

class MockBrevo implements BrevoInterface
{
    public array $campaigns = [];

    public array $campaignsStats = [];

    public array $reports = [];

    public array $reportExports = [];

    public array $campaignStatsCalls = [];

    public int $campaignReportCalls = 0;

    public int $campaignReportExportCalls = 0;

    public function sendEmailCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string
    {
        $id = (string) (count($this->campaigns) + 1);
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
            'scheduledAt' => $batchConfiguration ? new \DateTimeImmutable('now') : null,
            'batching' => $batchConfiguration,
        ];

        return $id;
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
