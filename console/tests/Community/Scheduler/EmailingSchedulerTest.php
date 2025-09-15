<?php

namespace App\Tests\Community\Scheduler;

use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Community\Scheduler\EmailingScheduler;
use SendGrid\Mail\Mail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class EmailingSchedulerTest extends KernelTestCase
{
    public function providerScheduleCampaign(): iterable
    {
        // 58000 emails to send with a throttle of 10000 per hour
        yield 'with-valid-high-throttling' => [
            'batchSize' => 100,
            'batchesCount' => 580,
            'throttlePerHour' => 10000,
            'expectedDelays' => [
                0 => null,
                15 => null,
                16 => 600000,
                32 => 1200000,
                48 => 1800000,
                64 => 2400000,
                579 => 21600000,
            ],
        ];

        // 58000 emails to send with a throttle of 200 per hour
        yield 'with-valid-low-throttling' => [
            'batchSize' => 100,
            'batchesCount' => 5,
            'throttlePerHour' => 200,
            'expectedDelays' => [
                0 => null,
                1 => 600000,
                2 => 1200000,
                3 => 1800000,
                4 => 2400000,
            ],
        ];

        // 58000 emails to send with a throttle of 50 per hour
        yield 'with-too-low-throttling' => [
            'batchSize' => 100,
            'batchesCount' => 580,
            'throttlePerHour' => 50,
            'expectedDelays' => [
                0 => null,
                15 => 9000000,
                30 => 18000000,
                579 => 347400000,
            ],
        ];

        // 58000 emails to send without throttling
        yield 'without-throttling' => [
            'batchSize' => 100,
            'batchesCount' => 580,
            'throttlePerHour' => null,
            'expectedDelays' => [
                0 => null,
                15 => null,
                16 => null,
                32 => null,
                48 => null,
                64 => null,
                579 => null,
            ],
        ];
    }

    /**
     * @dataProvider providerScheduleCampaign
     */
    public function testScheduleCampaign(int $batchSize, int $batchesCount, ?int $throttlePerHour, array $expectedDelays): void
    {
        self::bootKernel();

        /** @var EmailingScheduler $scheduler */
        $scheduler = static::getContainer()->get(EmailingScheduler::class);

        $scheduler->scheduleCampaign(
            messages: array_fill(0, $batchesCount, new SendgridMessage(new Mail())),
            batchSize: $batchSize,
            throttlePerHour: $throttlePerHour,
        );

        // Should have published batches with appropriate delays
        /** @var InMemoryTransport $transport */
        $transport = static::getContainer()->get('messenger.transport.async_emailing');

        $envelopes = $transport->getSent();
        $this->assertCount($batchesCount, $envelopes);

        foreach ($expectedDelays as $key => $expectedDelay) {
            /** @var Envelope $envelope */
            $envelope = $envelopes[$key];
            $this->assertInstanceOf(SendgridMessage::class, $envelope->getMessage());

            if ($expectedDelay) {
                $this->assertSame($expectedDelay, $envelope->all(DelayStamp::class)[0]->getDelay());
            } else {
                $this->assertSame([], $envelope->all(DelayStamp::class));
            }
        }
    }
}
