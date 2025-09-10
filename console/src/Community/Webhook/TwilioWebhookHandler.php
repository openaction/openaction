<?php

namespace App\Community\Webhook;

use App\Bridge\Twilio\TwilioInterface;
use App\Entity\Community\TextingCampaignMessage;
use App\Repository\Community\TextingCampaignMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TwilioWebhookHandler
{
    private TwilioInterface $twilio;
    private TextingCampaignMessageRepository $repository;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(TwilioInterface $t, TextingCampaignMessageRepository $r, EntityManagerInterface $em, LoggerInterface $l)
    {
        $this->twilio = $t;
        $this->repository = $r;
        $this->em = $em;
        $this->logger = $l;
    }

    public function __invoke(TwilioWebhookMessage $message)
    {
        if (!$this->twilio->verifySignature($message->getPayload(), $message->getSignature(), $message->getUri())) {
            $this->logger->error('Invalid Twilio signature received', $message->toArray());

            return true;
        }

        if (!isset($message->getPayload()['MessageStatus'])) {
            return true;
        }

        match ($message->getPayload()['MessageStatus']) {
            'sent' => $this->onMessageSent($message->getMessageId()),
            'delivered' => $this->onMessageDelivered($message->getMessageId()),
            'undelivered' => $this->onMessageUndelivered($message->getMessageId()),
            'failed' => $this->onMessageFailed($message->getMessageId()),
            default => null,
        };

        return true;
    }

    public function onMessageSent(string $messageId)
    {
        $this->handleMessageEvent($messageId, fn (TextingCampaignMessage $message) => $message->markSent());
    }

    public function onMessageDelivered(string $messageId)
    {
        $this->handleMessageEvent($messageId, fn (TextingCampaignMessage $message) => $message->markDelivered());
    }

    public function onMessageUndelivered(string $messageId)
    {
        $this->handleMessageEvent($messageId, fn (TextingCampaignMessage $message) => $message->markBounced());
    }

    public function onMessageFailed(string $messageId)
    {
        $this->handleMessageEvent($messageId, function (TextingCampaignMessage $message) {
            $message->markBounced();
            $message->getContact()->updateSmsSubscription(false, 'twilio:failed');
        });
    }

    private function handleMessageEvent(string $messageId, callable $updater)
    {
        if (!$message = $this->repository->find($messageId)) {
            return;
        }

        $updater($message);

        $this->em->persist($message);
        $this->em->persist($message->getContact());
        $this->em->flush();
    }
}
