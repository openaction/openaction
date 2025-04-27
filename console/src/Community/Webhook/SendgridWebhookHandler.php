<?php

namespace App\Community\Webhook;

use App\Bridge\Sendgrid\SendgridInterface;
use App\Entity\Community\EmailingCampaignMessage;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendgridWebhookHandler implements MessageHandlerInterface
{
    private SendgridInterface $sendgrid;
    private EmailingCampaignMessageRepository $repository;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(SendgridInterface $s, EmailingCampaignMessageRepository $r, EntityManagerInterface $em, LoggerInterface $l)
    {
        $this->sendgrid = $s;
        $this->repository = $r;
        $this->em = $em;
        $this->logger = $l;
    }

    public function __invoke(SendgridWebhookMessage $message)
    {
        try {
            $payload = Json::decode($message->getContent());
        } catch (\Exception) {
            $this->logger->error('Invalid Sendgrid payload received (not JSON)', $message->toArray());

            return true;
        }

        if (!$this->sendgrid->verifySignature($message->getContent(), $message->getSignature(), $message->getTimestamp())) {
            $this->logger->error('Invalid Sendgrid signature received', $message->toArray());

            return true;
        }

        /* @see \App\Community\SendgridWebhookListener */
        foreach ($payload as $event) {
            // Do not handle event without message ID
            if (empty($event['message-uuid'])) {
                continue;
            }

            match ($event['event']) {
                'delivered' => $this->onMessageDelivered($event),
                'bounce', 'dropped' => $this->onMessageBounced($event),
                'open' => $this->onMessageOpened($event),
                'click' => $this->onMessageClicked($event),
                'spamreport', 'unsubscribe' => $this->onMessageUnsubscribe($event),
                default => null,
            };
        }

        return true;
    }

    public function onMessageDelivered(array $payload)
    {
        $this->handleMessageEvent($payload, fn (EmailingCampaignMessage $message) => $message->markSent());
    }

    public function onMessageBounced(array $payload)
    {
        $this->handleMessageEvent($payload, static function (EmailingCampaignMessage $message) {
            $message->markBounced();
            $message->markUnsubscribed();
            $message->getContact()->updateNewsletterSubscription(subscribed: false, source: 'sendgrid:bounced');
        });
    }

    public function onMessageOpened(array $payload)
    {
        $this->handleMessageEvent($payload, fn (EmailingCampaignMessage $message) => $message->markOpened());
    }

    public function onMessageClicked(array $payload)
    {
        $this->handleMessageEvent($payload, fn (EmailingCampaignMessage $message) => $message->markClicked());
    }

    public function onMessageUnsubscribe(array $payload)
    {
        $this->handleMessageEvent($payload, static function (EmailingCampaignMessage $message) {
            $message->markUnsubscribed();
            $message->getContact()->updateNewsletterSubscription(subscribed: false, source: 'sendgrid:unsubscribe');
        });
    }

    private function handleMessageEvent(array $payload, callable $updater)
    {
        if (!$message = $this->repository->find((int) $payload['message-uuid'])) {
            return;
        }

        $updater($message);

        $this->em->persist($message);
        $this->em->persist($message->getContact());
        $this->em->flush();
    }
}
