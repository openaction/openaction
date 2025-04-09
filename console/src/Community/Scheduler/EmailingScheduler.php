<?php

namespace App\Community\Scheduler;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class EmailingScheduler
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function scheduleCampaign(array $messages, int $batchSize, ?int $throttlePerHour): void
    {
        if (null === $throttlePerHour) {
            $maxMessagesPer10minutes = max(count($messages), 1); // Disable throtlling by creating only one chunk
        } else {
            $maxMessagesPer10minutes = max(floor($throttlePerHour / $batchSize / 6), 1);
        }

        $delay = 0;
        foreach (array_chunk($messages, $maxMessagesPer10minutes) as $chunk) {
            foreach ($chunk as $message) {
                $this->bus->dispatch($message, $delay ? [new DelayStamp($delay)] : []);
            }

            $delay += 10 * 60 * 1000; // Send next chunk in 10min
        }
    }
}
