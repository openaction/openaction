<?php

namespace App\Controller\Bridge;

use App\Community\Webhook\TwilioWebhookMessage;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class TwilioController extends AbstractController
{
    #[Route('/webhook/twilio/{messageId}', name: 'webhook_twilio', methods: ['POST'], stateless: true)]
    public function webhook(MessageBusInterface $bus, Request $request, string $messageId)
    {
        $bus->dispatch(TwilioWebhookMessage::fromRequest($messageId, $request));

        return new JsonResponse([]);
    }
}
