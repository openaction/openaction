<?php

namespace App\Bridge\Twilio\Model;

class Personalization
{
    private string $to;
    private array $variables;
    private ?string $statusCallbackUrl;

    public function __construct(string $to, array $variables = [], string $statusCallbackUrl = null)
    {
        $this->to = $to;
        $this->variables = $variables;
        $this->statusCallbackUrl = $statusCallbackUrl;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getStatusCallbackUrl(): string
    {
        return $this->statusCallbackUrl;
    }
}
