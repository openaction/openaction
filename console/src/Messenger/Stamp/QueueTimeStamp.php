<?php

namespace App\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class QueueTimeStamp implements StampInterface
{
    private float $queueTime;

    public function __construct()
    {
        $this->queueTime = microtime(true);
    }

    public function getQueueTime(): float
    {
        return $this->queueTime;
    }
}
