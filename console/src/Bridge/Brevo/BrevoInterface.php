<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

interface BrevoInterface
{
    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string;

    public function getCampaignReport(string $apiKey, string $campaignId, ?string $campaignTag = null): array;
}
