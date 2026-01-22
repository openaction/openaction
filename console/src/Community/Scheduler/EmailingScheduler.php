<?php

namespace App\Community\Scheduler;

use App\Entity\Community\EmailBatch;

class EmailingScheduler
{
    /**
     * @param EmailBatch[]|iterable $batches
     */
    public function scheduleCampaign(iterable $batches, int $batchSize, ?int $throttlePerHour, ?\DateTimeInterface $now = null): void
    {
        if (null === $throttlePerHour) {
            $maxMessagesPer10minutes = PHP_INT_MAX; // Disable throttling by scheduling all batches at once
        } else {
            $maxMessagesPer10minutes = max((int) floor($throttlePerHour / $batchSize / 6), 1);
        }

        $baseTime = $now ? \DateTimeImmutable::createFromInterface($now) : new \DateTimeImmutable();
        $index = 0;

        foreach ($batches as $batch) {
            $chunkIndex = intdiv($index, $maxMessagesPer10minutes);
            $scheduledAt = $baseTime->modify(sprintf('+%d minutes', $chunkIndex * 10));
            $batch->setScheduledAt(\DateTime::createFromInterface($scheduledAt));
            ++$index;
        }
    }
}
