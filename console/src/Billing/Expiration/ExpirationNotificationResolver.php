<?php

namespace App\Billing\Expiration;

class ExpirationNotificationResolver
{
    /**
     * When to start sending expiration notifications, in days, relative to the expiration date.
     */
    private const START_DAY_BEFORE_EXPIRATION = 30;

    /**
     * The delay between notifications, starting from the start day.
     *
     * Here, notify at days 30, 20, 10, 5, 4, 3, 2, 1.
     */
    private const NOTIFICATIONS_DAYS = [0, 10, 10, 5, 1, 1, 1, 1];

    public function shouldSendNotification(\DateTime $currentPeriodEnd, array $lastNotificationsDates): bool
    {
        // Expires in a long time, shouldn't send
        if ((new \DateTime())->diff($currentPeriodEnd)->days > self::START_DAY_BEFORE_EXPIRATION) {
            return false;
        }

        $periodEnd = \DateTimeImmutable::createFromMutable($currentPeriodEnd);

        // Resolve when the notifications should start
        $startNotifyingOn = $periodEnd->modify('-'.self::START_DAY_BEFORE_EXPIRATION.' days');

        // Store all notifications that should be sent, then disable the one already sent or in the future
        $shouldNotify = array_fill(0, count(self::NOTIFICATIONS_DAYS), true);

        // Disable future notifications
        $delay = 0;
        foreach ($shouldNotify as $key => $v) {
            $delay += self::NOTIFICATIONS_DAYS[$key];

            if ($startNotifyingOn->modify('+'.$delay.' days') > new \DateTimeImmutable()) {
                $shouldNotify[$key] = false;
            }
        }

        // Disable already sent notifications
        foreach ($lastNotificationsDates as $date) {
            $daysDiff = $date->diff($currentPeriodEnd)->days;

            // Ignore past notifications older than START_DAY_BEFORE_EXPIRATION + 1
            if ($daysDiff > self::START_DAY_BEFORE_EXPIRATION + 1) {
                continue;
            }

            // Otherwise, disable notifications that were already sent
            $delay = 0;
            foreach ($shouldNotify as $key => $v) {
                $delay += self::NOTIFICATIONS_DAYS[$key];

                if ($startNotifyingOn->modify('+'.$delay.' days') < $date) {
                    $shouldNotify[$key] = false;
                }
            }
        }

        // Return true if at least one notification needs to be sent
        foreach ($shouldNotify as $v) {
            if ($v) {
                return true;
            }
        }

        return false;
    }
}
