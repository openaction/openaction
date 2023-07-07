<?php

namespace App\Bridge\Twilio\Model;

use libphonenumber\PhoneNumber;

class Recipient
{
    private PhoneNumber $number;
    private ?string $messageId;
    private array $vars;

    public function __construct(PhoneNumber $number, string $messageId = null, array $vars = [])
    {
        $this->number = $number;
        $this->messageId = $messageId;
        $this->vars = $vars;
    }

    public function getNumber(): PhoneNumber
    {
        return $this->number;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getVariables(): array
    {
        return $this->vars;
    }
}
