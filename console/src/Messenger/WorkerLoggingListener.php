<?php

namespace App\Messenger;

use App\Community\Consumer\SendBrevoEmailingCampaignMessage;
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
        $uniqueIdStamp = $envelope->last(UniqueIdStamp::class);
        $queueTimeStamp = $envelope->last(QueueTimeStamp::class);
        $queueTime = $queueTimeStamp?->getQueueTime();
        $startTimeStamp = $envelope->last(StartTimeStamp::class);
        $startTime = $startTimeStamp?->getStartTime();

        $messagePayload = $envelope->getMessage();
        $isBrevoCampaignSend = $messagePayload instanceof SendBrevoEmailingCampaignMessage;

        $context = [
            'hostname' => gethostname() ?: null,
            'messengerUniqueId' => $uniqueIdStamp?->getUniqueId(),
            'messageClass' => $messagePayload::class,
            'queueTimeMs' => $queueTime ? round((microtime(true) - $queueTime) * 1000) : 0,
            'durationMs' => $startTime ? round((microtime(true) - $startTime) * 1000) : 0,
            'state' => $type,
            'campaignId' => null,
            'sendToken' => null,
            'externalId' => null,
            'brevoMessageUniqueId' => null,
        ];

        if ($isBrevoCampaignSend) {
            /* @var SendBrevoEmailingCampaignMessage $messagePayload */
            $context['campaignId'] = $messagePayload->getCampaignId();
            $context['sendToken'] = $messagePayload->getSendToken();
            $context['brevoMessageUniqueId'] = $messagePayload->getMessengerUniqueId();
        }

        $logLevel = match ($type) {
            'failed' => 'error',
            'retried' => $isBrevoCampaignSend ? 'error' : 'warning',
            default => 'info',
        };

        $this->workerLogger->log($logLevel, $message, $context);
    }
}
