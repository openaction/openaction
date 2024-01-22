<?php

namespace App\Community\Webhook;

class ListUnsubscribeWebhookMessage
{
    private string $contactUuid;

    public function __construct(string $contactUuid)
    {
        $this->contactUuid = $contactUuid;
    }

    public function getContactUuid(): string
    {
        return $this->contactUuid;
    }
}
