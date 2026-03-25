<?php

namespace App\Community\Consumer;

use App\Util\Uid;

final class SendBrevoEmailingCampaignMessage
{
    private int $campaignId;
    private ?string $sendToken = null;
    private ?string $messengerUniqueId = null;

    public function __construct(int $campaignId, ?string $sendToken = null, ?string $messengerUniqueId = null)
    {
        $this->campaignId = $campaignId;
        $sendToken = null !== $sendToken ? trim($sendToken) : null;
        $this->sendToken = '' === $sendToken ? null : $sendToken;
        $this->messengerUniqueId = $messengerUniqueId ?: null;
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    public function getSendToken(): ?string
    {
        return $this->sendToken;
    }

    public function getMessengerUniqueId(): string
    {
        if (!$this->messengerUniqueId) {
            $this->messengerUniqueId = Uid::toBase62(Uid::random());
        }

        return $this->messengerUniqueId;
    }
}
