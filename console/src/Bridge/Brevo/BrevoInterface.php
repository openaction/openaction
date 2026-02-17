<?php

namespace App\Bridge\Brevo;

use App\Entity\Community\EmailingCampaign;

interface BrevoInterface
{
    public function sendEmailCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string;

    public function createCampaignList(EmailingCampaign $campaign): int;

    public function syncCampaignContacts(EmailingCampaign $campaign, int $listId, array $contacts): void;

    public function createEmailCampaign(EmailingCampaign $campaign, string $htmlContent, int $listId): string;

    public function isEmailCampaignSent(EmailingCampaign $campaign, string $campaignId): bool;

    public function sendEmailCampaignNow(EmailingCampaign $campaign, string $campaignId): void;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getEmailCampaignsStats(
        string $apiKey,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
    ): array;

    public function getEmailCampaignReport(string $apiKey, string $campaignId): array;

    public function exportEmailCampaignRecipients(string $apiKey, string $campaignId): string;
}
