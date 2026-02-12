<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

class MockBrevo implements BrevoInterface
{
    public array $campaigns = [];

    public array $reports = [];

    /**
     * @return string[]
     */
    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): array
    {
        $throttlingPerHour = $campaign->getProject()->getOrganization()->getEmailThrottlingPerHour();
        $listCapacity = (!$throttlingPerHour || $throttlingPerHour <= 0) ? null : max((int) floor($throttlingPerHour / 4), 1);
        $contactChunks = $listCapacity ? array_chunk($contacts, $listCapacity) : [$contacts];
        $baseScheduledAt = new \DateTimeImmutable('now');
        $createdIds = [];

        if (!$contactChunks) {
            $contactChunks = [[]];
        }

        foreach ($contactChunks as $chunkIndex => $contactChunk) {
            $id = (string) (count($this->campaigns) + 1);

            $this->campaigns[$id] = [
                'campaign' => $campaign,
                'html' => $htmlContent,
                'contacts' => $contactChunk,
                'scheduledAt' => $listCapacity
                    ? $baseScheduledAt->modify(sprintf('+%d minutes', $chunkIndex * 15))
                    : null,
            ];

            $createdIds[] = $id;
        }

        return $createdIds;
    }

    public function getCampaignReport(string $apiKey, string $campaignId): array
    {
        return $this->reports[$campaignId] ?? [];
    }
}
