<?php

namespace App\Entity\Billing\Model;

use App\Util\Json;

class OrderAction
{
    public const ADD_EMAIL_CREDITS = 'add_email_credits';
    public const ADD_TEXT_CREDITS = 'add_text_credits';
    public const PRINT = 'print';
    public const NOTHING = 'nothing';

    private string $type;
    private array $payload;

    public function __construct(string $type, array $payload = [])
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    public function __toString(): string
    {
        return $this->type.': '.Json::encode($this->payload);
    }

    public static function addEmailCredits(int $credits): self
    {
        return new self(self::ADD_EMAIL_CREDITS, ['credits' => $credits]);
    }

    public static function addTextCredits(int $credits): self
    {
        return new self(self::ADD_TEXT_CREDITS, ['credits' => $credits]);
    }

    public static function print(string $orderUuid): self
    {
        return new self(self::PRINT, ['orderUuid' => $orderUuid]);
    }

    public static function fromArray(array $data): self
    {
        return new self($data['type'], $data['payload']);
    }

    public function toArray(): array
    {
        return ['type' => $this->type, 'payload' => $this->payload];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
