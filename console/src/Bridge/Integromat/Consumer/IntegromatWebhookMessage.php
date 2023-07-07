<?php

namespace App\Bridge\Integromat\Consumer;

final class IntegromatWebhookMessage
{
    private int $webhookId;
    private string $url;
    private array $payload;

    public function __construct(int $webhookId, string $url, array $payload)
    {
        $this->webhookId = $webhookId;
        $this->url = $url;
        $this->payload = $payload;
    }

    public function getWebhookId(): int
    {
        return $this->webhookId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
