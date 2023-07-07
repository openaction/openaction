<?php

namespace App\Entity\Model;

class SubscriptionNotifications
{
    public const TYPE_EXPIRATION = 'expiration';

    private array $notifications;

    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
    }

    public function toArray(): array
    {
        return $this->notifications;
    }

    public function withMarkedNotified(string $type, \DateTime $date): self
    {
        $this->notifications[$type][] = $date->format('Y-m-d H:i:s');

        return $this;
    }

    public function getDatesFor(string $type): array
    {
        if (!isset($this->notifications[$type])) {
            return [];
        }

        $dates = [];
        foreach ($this->notifications[$type] as $date) {
            $dates[] = new \DateTime($date);
        }

        return $dates;
    }
}
