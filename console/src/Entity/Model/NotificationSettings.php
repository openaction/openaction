<?php

namespace App\Entity\Model;

class NotificationSettings
{
    public const EVENT_PROJECT_CREATED = 'project_created';
    public const EVENT_NEWSLETTER_SENT = 'newsletter_sent';
    public const EVENT_LOW_CREDITS = 'low_credits';
    public const EVENT_FEATURE_ADDED = 'feature_added';

    private array $events;

    public function __construct(array $events)
    {
        $this->events = array_intersect($events, self::getAllEvents());
    }

    public function isEnabled(string $event): bool
    {
        return in_array($event, $this->events, true);
    }

    public function toArray(): array
    {
        return $this->events;
    }

    public static function getAllEvents(): array
    {
        return [
            self::EVENT_PROJECT_CREATED,
            self::EVENT_NEWSLETTER_SENT,
            self::EVENT_LOW_CREDITS,
            self::EVENT_FEATURE_ADDED,
        ];
    }
}
