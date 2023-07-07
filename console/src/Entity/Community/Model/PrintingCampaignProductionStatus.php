<?php

namespace App\Entity\Community\Model;

class PrintingCampaignProductionStatus
{
    public const PREPARING = 'preparing';
    public const ORDERED = 'ordered';
    public const PRINTING = 'printing';
    public const DELIVERING = 'delivering';
    public const DELIVERED = 'delivered';

    private string $status = self::PREPARING;
    private ?string $trackingCode = null;
    private array $logs = [];

    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->status = $data['status'];
        $self->trackingCode = $data['trackingCode'];
        $self->logs = $data['logs'];

        return $self;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'trackingCode' => $this->trackingCode,
            'logs' => $this->logs,
        ];
    }

    public function withStatusUpdate(string $newStatus, string $trackingCode = null): self
    {
        $self = clone $this;
        $self->status = $newStatus;
        $self->trackingCode = $self->trackingCode ?: $trackingCode;
        $self->logs[] = [date('Y-m-d H:i:s'), $newStatus, $trackingCode];

        return $self;
    }
}
