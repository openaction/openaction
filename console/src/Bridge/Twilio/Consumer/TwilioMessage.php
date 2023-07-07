<?php

namespace App\Bridge\Twilio\Consumer;

use App\Bridge\Twilio\Model\Personalization;

final class TwilioMessage
{
    private ?string $from;
    private string $body;

    /** @var Personalization[] */
    private array $personalizations;

    public function __construct(?string $from, string $body, array $personalizations)
    {
        $this->from = $from;
        $this->body = $body;
        $this->personalizations = $personalizations;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getPersonalizations(): array
    {
        return $this->personalizations;
    }
}
