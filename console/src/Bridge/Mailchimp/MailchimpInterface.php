<?php

namespace App\Bridge\Mailchimp;

use App\Entity\Community\EmailingCampaign;

interface MailchimpInterface
{
    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string;

    public function getCampaignReport(string $apiKey, string $serverPrefix, string $campaignId): array;
}
