<?php

namespace App\Controller\Bridge;

use App\Billing\Event\MollieEvent;
use App\Bridge\Mollie\MollieInterface;
use App\Controller\AbstractController;
use App\Entity\Billing\Order;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/webhook/mollie', stateless: true)]
class MollieController extends AbstractController
{
    private EventDispatcherInterface $eventDispatcher;
    private MollieInterface $mollie;

    public function __construct(EventDispatcherInterface $ed, MollieInterface $mollie)
    {
        $this->eventDispatcher = $ed;
        $this->mollie = $mollie;
    }

    #[Route('/{uuid}/event', name: 'webhook_mollie_event', stateless: true)]
    public function event(Order $order)
    {
        // Find Mollie order
        if (!$mollieOrder = $this->mollie->getOrder($order->getMollieId())) {
            throw $this->createNotFoundException('Mollie order not found');
        }

        // Dispatch hook
        /* @see \App\Billing\MollieWebhookListener */
        $this->eventDispatcher->dispatch(new MollieEvent($order, $mollieOrder), 'mollie.'.$mollieOrder->status);

        return new JsonResponse([]);
    }
}
