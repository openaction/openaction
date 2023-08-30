<?php

namespace App\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class StartTimeStamp implements StampInterface
{
    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }
}
