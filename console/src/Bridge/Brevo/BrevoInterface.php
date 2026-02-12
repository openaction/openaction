<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

interface BrevoInterface
{
    /**
     * @return string[]
     */
    public function sendEmailCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): array;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getEmailCampaignsStats(string $apiKey): array;

    public function getEmailCampaignReport(string $apiKey, string $campaignId): array;
}
