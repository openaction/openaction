<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

class MockBrevo implements BrevoInterface
{
    public array $campaigns = [];

    public array $reports = [];

    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string
    {
        $id = (string) (count($this->campaigns) + 1);

        $this->campaigns[$id] = [
            'campaign' => $campaign,
            'html' => $htmlContent,
            'contacts' => $contacts,
        ];

        return $id;
    }

    public function getCampaignReport(string $apiKey, string $campaignId, ?string $campaignTag = null): array
    {
        return $this->reports[$campaignId] ?? [];
    }
}
