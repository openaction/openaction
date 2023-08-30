<?php

namespace App\Tests\Controller\Bridge;

use App\Billing\Invoice\GenerateInvoicePdfMessage;
use App\Entity\Billing\Order;
use App\Repository\Billing\OrderRepository;
use App\Tests\WebTestCase;

class MollieControllerTest extends WebTestCase
{
    public function testExpired()
    {
        $client = static::createClient();

        /** @var Order $order */
        $order = self::getContainer()->get(OrderRepository::class)->findOneBy(['uuid' => 'bdd21e4d-93f5-4c1c-97fd-7ba730ee4394']);
        $this->assertInstanceOf(Order::class, $order);

        // Call hook
        $client->request('POST', '/webhook/mollie/bdd21e4d-93f5-4c1c-97fd-7ba730ee4394/event', ['id' => 'ord_bdd21e4d']);
        $this->assertResponseIsSuccessful();

        // Should have been removed
        $this->assertNull(self::getContainer()->get(OrderRepository::class)->findOneBy(['uuid' => 'bdd21e4d-93f5-4c1c-97fd-7ba730ee4394']));
    }

    public function testPaid()
    {
        $client = static::createClient();

        /** @var Order $order */
        $order = self::getContainer()->get(OrderRepository::class)->findOneBy(['uuid' => '35bc37bc-8e8a-4d37-b5c5-9ec5a62eee28']);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertNull($order->getPaidAt());
        $this->assertNull($order->getInvoiceNumber());
        $this->assertNull($order->getInvoicePdf());

        $oldCredits = $order->getOrganization()->getCreditsBalance();

        // Call hook
        $client->request('POST', '/webhook/mollie/35bc37bc-8e8a-4d37-b5c5-9ec5a62eee28/event', ['id' => 'ord_35bc37bc']);
        $this->assertResponseIsSuccessful();

        // Order should have been updated
        $order = self::getContainer()->get(OrderRepository::class)->find($order->getId());
        $this->assertNotNull($order->getPaidAt());
        $this->assertNotNull($order->getInvoiceNumber());
        $this->assertNull($order->getInvoicePdf());

        // Organization should have been granted credits
        $this->assertSame($oldCredits + 1000, $order->getOrganization()->getCreditsBalance());

        // Invoice generation message should have been published
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());
        $this->assertInstanceOf(GenerateInvoicePdfMessage::class, $messages[0]->getMessage());
    }
}
