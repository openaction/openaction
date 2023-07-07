<?php

namespace App\Tests\Entity;

use App\Entity\Organization;
use App\Entity\SubscriptionLog;
use App\Tests\UnitTestCase;

class OrganizationTest extends UnitTestCase
{
    public function provideIsExpired()
    {
        yield ['subscriptionCurrentPeriodEnd' => '-2 days', 'expectedExpired' => true];
        yield ['subscriptionCurrentPeriodEnd' => '-6 hours', 'expectedExpired' => false];
        yield ['subscriptionCurrentPeriodEnd' => '+1 day', 'expectedExpired' => false];
        yield ['subscriptionCurrentPeriodEnd' => '+15 days', 'expectedExpired' => false];
        yield ['subscriptionCurrentPeriodEnd' => '+1 year', 'expectedExpired' => false];
        yield ['subscriptionCurrentPeriodEnd' => null, 'expectedExpired' => true];
    }

    /**
     * @dataProvider provideIsExpired
     */
    public function testIsExpired(?string $currentPeriodEndFormat, bool $expectedExpired)
    {
        $currentPeriodEnd = $currentPeriodEndFormat ? new \DateTime($currentPeriodEndFormat) : null;

        $orga = new Organization('Citipo');
        $this->setProperty($orga, 'subscriptionTrialing', false);
        $this->setProperty($orga, 'subscriptionCurrentPeriodEnd', $currentPeriodEnd);

        $this->assertSame($expectedExpired, $orga->isSubscriptionExpired());
        $this->assertSame(!$expectedExpired, $orga->isSubscriptionActive());
    }

    public function testAddUseEmailCreditsValid()
    {
        $orga = new Organization('Citipo');
        $this->assertSame(0, $orga->getCreditsBalance());
        $this->assertSame([], $orga->getSubscriptionLogs()->map(fn (SubscriptionLog $log) => $log->getMessage())->toArray());

        $orga->addCredits(1000);
        $this->assertSame(1000, $orga->getCreditsBalance());
        $this->assertSame(
            ['credits_added'],
            $orga->getSubscriptionLogs()->map(fn (SubscriptionLog $log) => $log->getMessage())->toArray()
        );

        $orga->useCredits(250, 'campaign');
        $this->assertSame(750, $orga->getCreditsBalance());
        $this->assertSame(
            ['credits_added', 'credits_used'],
            $orga->getSubscriptionLogs()->map(fn (SubscriptionLog $log) => $log->getMessage())->toArray()
        );
    }

    public function testAddUseTextCreditsValid()
    {
        $orga = new Organization('Citipo');
        $this->assertSame(0, $orga->getTextsCreditsBalance());
        $this->assertSame([], $orga->getSubscriptionLogs()->map(fn (SubscriptionLog $log) => $log->getMessage())->toArray());

        $orga->addTextsCredits(1000);
        $this->assertSame(1000, $orga->getTextsCreditsBalance());
        $this->assertSame(
            ['texts_credits_added'],
            $orga->getSubscriptionLogs()->map(fn (SubscriptionLog $log) => $log->getMessage())->toArray()
        );

        $orga->useTextsCredits(250, 'campaign');
        $this->assertSame(750, $orga->getTextsCreditsBalance());
        $this->assertSame(
            ['texts_credits_added', 'texts_credits_used'],
            $orga->getSubscriptionLogs()->map(fn (SubscriptionLog $log) => $log->getMessage())->toArray()
        );
    }
}
