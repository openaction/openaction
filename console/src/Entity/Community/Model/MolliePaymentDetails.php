<?php

namespace App\Entity\Community\Model;

class MolliePaymentDetails
{
    public function __construct(
        public string $transactionId,
        public string $method,
        public ?string $subscriptionId = null,
        public ?string $cardBrand = null,
        public ?string $cardLast4 = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['transactionId'] ?? '',
            $data['method'] ?? '',
            $data['subscriptionId'] ?? null,
            $data['cardBrand'] ?? null,
            $data['cardLast4'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'transactionId' => $this->transactionId,
            'method' => $this->method,
            'subscriptionId' => $this->subscriptionId,
            'cardBrand' => $this->cardBrand,
            'cardLast4' => $this->cardLast4,
        ];
    }
}
