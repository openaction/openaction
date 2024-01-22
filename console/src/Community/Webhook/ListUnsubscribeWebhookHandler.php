<?php

namespace App\Community\Webhook;

use App\Repository\Community\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ListUnsubscribeWebhookHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly ContactRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(ListUnsubscribeWebhookMessage $message)
    {
        if (!$contact = $this->repository->findOneByBase62Uid($message->getContactUuid())) {
            return;
        }

        $contact->updateNewsletterSubscription(subscribed: false, source: 'list:unsubscribe');

        $this->em->persist($contact);
        $this->em->flush();
    }
}
