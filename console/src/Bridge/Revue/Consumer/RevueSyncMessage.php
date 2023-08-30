<?php

namespace App\Bridge\Revue\Consumer;

final class RevueSyncMessage
{
    private int $accountId;

    public function __construct(int $accountId)
    {
        $this->accountId = $accountId;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }
}
