<?php

namespace App\Bridge\Mailchimp;

use App\Entity\Community\EmailingCampaign;

interface MailchimpInterface
{
    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string;
}
