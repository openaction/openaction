<?php

namespace App\Bridge\Spallian\Consumer;

final class SpallianMessage
{
    private string $endpoint;
    private array $payload;

    public function __construct(string $endpoint, array $payload)
    {
        $this->endpoint = $endpoint;
        $this->payload = $payload;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
