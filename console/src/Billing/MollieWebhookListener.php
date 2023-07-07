<?php

namespace App\Billing;

use App\Billing\Event\MollieEvent;
use App\Billing\Invoice\GenerateInvoicePdfMessage;
use App\Community\Printing\PrintingWorkflow;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Community\PrintingOrder;
use App\Repository\Billing\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MollieWebhookListener implements EventSubscriberInterface
{
    private OrderRepository $orderRepository;
    private EntityManagerInterface $em;
    private PrintingWorkflow $printingWorkflow;
    private MessageBusInterface $bus;

    public function __construct(OrderRepository $or, EntityManagerInterface $em, PrintingWorkflow $pw, MessageBusInterface $bus)
    {
        $this->orderRepository = $or;
        $this->em = $em;
        $this->printingWorkflow = $pw;
        $this->bus = $bus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'mollie.canceled' => 'onOrderCanceledOrExpired',
            'mollie.expired' => 'onOrderCanceledOrExpired',
            'mollie.paid' => 'onOrderPaid',
        ];
    }

    public function onOrderCanceledOrExpired(MollieEvent $event)
    {
        $this->em->remove($event->getOrder());
        $this->em->flush();
    }

    public function onOrderPaid(MollieEvent $event)
    {
        // Already paid, ignore
        if ($event->getOrder()->getPaidAt()) {
            return;
        }

        // Mark the order as paid
        $event->getOrder()->markPaid(
            $this->orderRepository->findNextInvoiceNumber(),
            new \DateTime($event->getMollieOrder()->paidAt ?: 'now')
        );

        // Apply the action
        match ($event->getOrder()->getAction()->getType()) {
            OrderAction::ADD_EMAIL_CREDITS => $this->addEmailCredits($event),
            OrderAction::ADD_TEXT_CREDITS => $this->addTextCredits($event),
            OrderAction::PRINT => $this->printOrder($event),
            default => 'no-op',
        };

        // Persist
        $this->em->persist($event->getOrder()->getOrganization());
        $this->em->persist($event->getOrder());
        $this->em->flush();

        // Generate the invoice
        $this->bus->dispatch(new GenerateInvoicePdfMessage($event->getOrder()->getId()));
    }

    private function addEmailCredits(MollieEvent $event)
    {
        $event->getOrder()->getOrganization()->addCredits(
            $event->getOrder()->getAction()->getPayload()['credits']
        );
    }

    private function addTextCredits(MollieEvent $event)
    {
        $event->getOrder()->getOrganization()->addTextsCredits(
            $event->getOrder()->getAction()->getPayload()['credits']
        );
    }

    private function printOrder(MollieEvent $event)
    {
        $order = $this->em->getRepository(PrintingOrder::class)->findOneBy([
            'uuid' => $event->getOrder()->getAction()->getPayload()['orderUuid'],
        ]);

        if ($order) {
            $this->printingWorkflow->receivePayment($order);
        }
    }
}
