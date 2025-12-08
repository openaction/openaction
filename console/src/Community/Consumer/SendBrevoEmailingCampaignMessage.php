<?php

namespace App\Community\Consumer;

final class SendBrevoEmailingCampaignMessage
{
    public function __construct(private readonly int $campaignId)
    {
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }
}
