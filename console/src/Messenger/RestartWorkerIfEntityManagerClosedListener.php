<?php

namespace App\Messenger;

use App\Messenger\Exception\RestartWorkerException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class RestartWorkerIfEntityManagerClosedListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onMessageFailed',
        ];
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event)
    {
        if (!str_contains($event->getThrowable()->getMessage(), 'The EntityManager is closed')) {
            return;
        }

        throw new RestartWorkerException('Worker stopped due to the EntityManager being closed');
    }
}
