<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

interface BrevoInterface
{
    /**
     * @return string[]
     */
    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): array;

    public function getCampaignReport(string $apiKey, string $campaignId): array;
}
