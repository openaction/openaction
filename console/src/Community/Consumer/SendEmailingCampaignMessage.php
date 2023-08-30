<?php

namespace App\Community\Consumer;

final class SendEmailingCampaignMessage
{
    private int $campaignId;

    public function __construct(int $campaignId)
    {
        $this->campaignId = $campaignId;
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }
}
