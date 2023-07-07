<?php

namespace App\Bridge\Integromat;

use App\Api\Transformer\Community\ContactTransformer;
use App\Bridge\Integromat\Consumer\IntegromatWebhookMessage;
use App\Entity\Community\Contact;
use App\Repository\Integration\IntegromatWebhookRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class Integromat implements IntegromatInterface
{
    private IntegromatWebhookRepository $repository;
    private ContactTransformer $transformer;
    private MessageBusInterface $bus;

    public function __construct(IntegromatWebhookRepository $r, ContactTransformer $t, MessageBusInterface $bus)
    {
        $this->repository = $r;
        $this->transformer = $t;
        $this->bus = $bus;
    }

    public function triggerWebhooks(Contact $contact)
    {
        if (!$orga = $contact->getOrganization()) {
            return;
        }

        if (!$webhooks = $this->repository->findBy(['organization' => $orga])) {
            return;
        }

        $payload = $this->transformer->transform($contact);

        foreach ($webhooks as $webhook) {
            $this->bus->dispatch(new IntegromatWebhookMessage($webhook->getId(), $webhook->getIntegromatUrl(), $payload));
        }
    }
}
