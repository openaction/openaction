<?php

namespace App\Tests\Billing;

use App\Billing\Event\MollieEvent;
use App\Billing\Invoice\GenerateInvoicePdfMessage;
use App\Billing\MollieWebhookListener;
use App\Bridge\Mollie\MollieInterface;
use App\Entity\Billing\Order;
use App\Repository\Billing\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MollieWebhookTest extends KernelTestCase
{
    public function providePaid()
    {
        yield 'add-emails' => [
            'orderUuid' => '35bc37bc-8e8a-4d37-b5c5-9ec5a62eee28',
            'assertAction' => static function (KernelTestCase $case, Order $order) {
                $case->assertSame(6000, $order->getOrganization()->getCreditsBalance());
            },
        ];

        yield 'add-texts' => [
            'orderUuid' => '7698f242-0a05-496c-b542-34236e9de12c',
            'assertAction' => static function (KernelTestCase $case, Order $order) {
                $case->assertSame(160, $order->getOrganization()->getTextsCreditsBalance());
            },
        ];
    }

    /**
     * @dataProvider providePaid
     */
    public function testPaid(string $orderUuid, callable $assertAction)
    {
        self::bootKernel();

        /** @var Order $order */
        $order = static::getContainer()->get(OrderRepository::class)->findOneByUuid($orderUuid);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertNull($order->getPaidAt());
        $this->assertNull($order->getInvoiceNumber());
        $this->assertNull($order->getInvoicePdf());

        $mollieOrder = static::getContainer()->get(MollieInterface::class)->getOrder($order->getMollieId());

        // Trigger listener
        static::getContainer()->get(MollieWebhookListener::class)->onOrderPaid(new MollieEvent($order, $mollieOrder));

        // Check data
        /** @var Order $order */
        $order = static::getContainer()->get(OrderRepository::class)->findOneByUuid($orderUuid);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertNotNull($order->getPaidAt());
        $this->assertNotNull($order->getInvoiceNumber());
        $this->assertNull($order->getInvoicePdf());

        // Check the action was applied
        $assertAction($this, $order);

        // Check the invoice generation was scheduled
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var GenerateInvoicePdfMessage $message */
        $this->assertInstanceOf(GenerateInvoicePdfMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($order->getId(), $message->getOrderId());
    }

    public function testRemoveExpired()
    {
        self::bootKernel();

        /** @var Order $order */
        $order = static::getContainer()->get(OrderRepository::class)->findOneByUuid('bdd21e4d-93f5-4c1c-97fd-7ba730ee4394');
        $this->assertInstanceOf(Order::class, $order);

        $mollieOrder = static::getContainer()->get(MollieInterface::class)->getOrder($order->getMollieId());

        // Trigger listener
        static::getContainer()->get(MollieWebhookListener::class)->onOrderCanceledOrExpired(new MollieEvent($order, $mollieOrder));

        // Check removal
        $this->assertNull(static::getContainer()->get(OrderRepository::class)->findOneByUuid('bdd21e4d-93f5-4c1c-97fd-7ba730ee4394'));

        // Check nothing published
        $this->assertCount(0, static::getContainer()->get('messenger.transport.async_priority_high')->get());
    }
}
