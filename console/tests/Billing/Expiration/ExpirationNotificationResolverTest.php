<?php

namespace App\Tests\Billing\Expiration;

use App\Billing\Expiration\ExpirationNotificationResolver;
use App\Tests\UnitTestCase;

class ExpirationNotificationResolverTest extends UnitTestCase
{
    public function provideShouldSendNotification()
    {
        yield 'long' => [
            'periodEnd' => '+90 days',
            'lastNotifications' => [],
            'expected' => false,
        ];

        yield '30 days' => [
            'periodEnd' => '+29 days',
            'lastNotifications' => [],
            'expected' => true,
        ];

        yield '30 days old notification' => [
            'periodEnd' => '+29 days',
            'lastNotifications' => ['-1 year'],
            'expected' => true,
        ];

        yield '30 days already notified' => [
            'periodEnd' => '+29 days',
            'lastNotifications' => ['+30 days'],
            'expected' => false,
        ];

        yield '20 days' => [
            'periodEnd' => '+19 days',
            'lastNotifications' => [],
            'expected' => true,
        ];

        yield '20 days already notified' => [
            'periodEnd' => '+19 days',
            'lastNotifications' => ['+30 days', '+20 days'],
            'expected' => false,
        ];

        yield '20 days missing' => [
            'periodEnd' => '+19 days',
            'lastNotifications' => ['+30 days', '+10 days'],
            'expected' => false,
        ];

        yield '10 days' => [
            'periodEnd' => '+9 days',
            'lastNotifications' => [],
            'expected' => true,
        ];

        yield '10 days already notified' => [
            'periodEnd' => '+9 days',
            'lastNotifications' => ['+30 days', '+20 days', '+10 days'],
            'expected' => false,
        ];

        yield '5 days' => [
            'periodEnd' => '+4 days',
            'lastNotifications' => [],
            'expected' => true,
        ];

        yield '5 days already notified' => [
            'periodEnd' => '+4 days',
            'lastNotifications' => ['+30 days', '+20 days', '+10 days', '+5 days'],
            'expected' => false,
        ];

        yield '4 days today' => [
            'periodEnd' => '+4 days',
            'lastNotifications' => [],
            'expected' => true,
        ];

        yield '4 days today already notified' => [
            'periodEnd' => '+4 days',
            'lastNotifications' => ['+30 days', '+20 days', '+10 days', '+5 days', '+4 days'],
            'expected' => false,
        ];

        yield '1 day today' => [
            'periodEnd' => '+2 hours',
            'lastNotifications' => [],
            'expected' => true,
        ];

        yield '1 day today already notified' => [
            'periodEnd' => '+2 hours',
            'lastNotifications' => ['+30 days', '+20 days', '+10 days', '+5 days', '+4 days', '+4 hours'],
            'expected' => false,
        ];
    }

    /**
     * @dataProvider provideShouldSendNotification
     */
    public function testShouldSendNotification(string $periodEnd, array $lastNotifications, bool $expected)
    {
        $resolver = new ExpirationNotificationResolver();

        $this->assertSame($expected, $resolver->shouldSendNotification(
            new \DateTime($periodEnd),
            array_map(static fn ($n) => new \DateTime($n), $lastNotifications),
        ));
    }
}
