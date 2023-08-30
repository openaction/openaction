<?php

namespace App\Messenger;

use App\Messenger\Stamp\QueueTimeStamp;
use App\Messenger\Stamp\StartTimeStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class TimeMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // Compute queue time as soon as the message is sent (on the UI process)
        if (null === $envelope->last(QueueTimeStamp::class)) {
            $envelope = $envelope->with(new QueueTimeStamp());
        }

        // If the envelope has been received but no start time has been computed yet, compute it
        if ($envelope->last(ReceivedStamp::class) && null === $envelope->last(StartTimeStamp::class)) {
            $envelope = $envelope->with(new StartTimeStamp());
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
