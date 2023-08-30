<?php

namespace App\Controller\Bridge;

use App\Community\Webhook\SendgridWebhookMessage;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class SendgridController extends AbstractController
{
    #[Route('/webhook/sendgrid', name: 'webhook_sendgrid', methods: ['POST'], stateless: true)]
    public function webhook(MessageBusInterface $bus, Request $request)
    {
        // Does not contain JSON => invalid
        if (!str_contains($request->getContent(), '{')) {
            return new JsonResponse([]);
        }

        $bus->dispatch(SendgridWebhookMessage::fromRequest($request));

        return new JsonResponse([]);
    }
}
