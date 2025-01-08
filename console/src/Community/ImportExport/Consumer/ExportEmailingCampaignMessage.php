<?php

namespace App\Community\ImportExport\Consumer;

final class ExportEmailingCampaignMessage
{
    private string $locale;
    private string $email;
    private int $campaignId;

    public function __construct(string $locale, string $email, int $campaignId)
    {
        $this->locale = $locale;
        $this->email = $email;
        $this->campaignId = $campaignId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }
}
