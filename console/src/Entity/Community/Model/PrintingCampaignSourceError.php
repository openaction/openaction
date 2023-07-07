<?php

namespace App\Entity\Community\Model;

class PrintingCampaignSourceError
{
    private array $messages = [];
    private \DateTime $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->date = new \DateTime($data['date']) ?? new \DateTime();
        $self->messages = $data['messages'] ?? [];

        return $self;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date->format('Y-m-d H:i:s'),
            'messages' => $this->messages,
        ];
    }

    public function withMessage(string $message): self
    {
        $self = clone $this;
        $self->messages[] = $message;

        return $self;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }
}
