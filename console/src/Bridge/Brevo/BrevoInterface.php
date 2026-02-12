<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

interface BrevoInterface
{
    public function sendEmailCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getEmailCampaignsStats(
        string $apiKey,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
    ): array;

    public function getEmailCampaignReport(string $apiKey, string $campaignId): array;
}
