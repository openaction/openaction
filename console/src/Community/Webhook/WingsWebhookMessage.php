<?php

namespace App\Community\Webhook;

class WingsWebhookMessage
{
    public function __construct(private int $projectId, private array $payload)
    {
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
