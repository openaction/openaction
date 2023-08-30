<?php

namespace App\Messenger;

use App\Messenger\Stamp\QueueTimeStamp;
use App\Messenger\Stamp\StartTimeStamp;
use App\Messenger\Stamp\UniqueIdStamp;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageRetriedEvent;

class WorkerLoggingListener implements EventSubscriberInterface
{
    private LoggerInterface $workerLogger;

    public function __construct(LoggerInterface $workerLogger)
    {
        $this->workerLogger = $workerLogger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => 'onMessageHandled',
            WorkerMessageFailedEvent::class => 'onMessageFailed',
            WorkerMessageRetriedEvent::class => 'onMessageRetried',
        ];
    }

    public function onMessageHandled(WorkerMessageHandledEvent $event)
    {
        $this->log($event->getEnvelope(), 'handled', 'Message handled successfully (acknowledging to transport)');
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event)
    {
        $this->log($event->getEnvelope(), 'failed', 'Message failed to be handled');
    }

    public function onMessageRetried(WorkerMessageRetriedEvent $event)
    {
        $this->log($event->getEnvelope(), 'retried', 'Message has been sent for retry after failure');
    }

    private function log(Envelope $envelope, string $type, string $message)
    {
        /** @var UniqueIdStamp $uniqueIdStamp */
        $uniqueIdStamp = $envelope->last(UniqueIdStamp::class);

        /** @var QueueTimeStamp $queueTimeStamp */
        $queueTimeStamp = $envelope->last(QueueTimeStamp::class);
        $queueTime = $queueTimeStamp?->getQueueTime();

        /** @var StartTimeStamp $startTimeStamp */
        $startTimeStamp = $envelope->last(StartTimeStamp::class);
        $startTime = $startTimeStamp?->getStartTime();

        $this->workerLogger->info(implode(' ', [
            '['.gethostname().']',
            '['.$uniqueIdStamp->getUniqueId().']',
            '['.$envelope->getMessage()::class.']',
            '['.($queueTime ? round((microtime(true) - $queueTime) * 1000) : 0).']',
            '['.($startTime ? round((microtime(true) - $startTime) * 1000) : 0).']',
            '['.$type.']',
            $message,
        ]));
    }
}
