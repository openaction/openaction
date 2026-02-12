<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

interface BrevoInterface
{
    public function sendEmailCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getEmailCampaignsStats(string $apiKey): array;

    public function getEmailCampaignReport(string $apiKey, string $campaignId): array;
}
