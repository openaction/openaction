<?php

namespace App\Tests\Billing\Invoice;

use App\Billing\Invoice\GenerateInvoicePdfHandler;
use App\Billing\Invoice\GenerateInvoicePdfMessage;
use App\Entity\Billing\Order;
use App\Repository\Billing\OrderRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class GenerateInvoicePdfHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(GenerateInvoicePdfHandler::class);
        $handler(new GenerateInvoicePdfMessage(0));

        // Shouldn't have done anything
        $this->assertQueuedEmailCount(0);
    }

    public function testConsumeNotPaid()
    {
        self::bootKernel();

        /** @var Order $order */
        $order = static::getContainer()->get(OrderRepository::class)->findOneByUuid('35bc37bc-8e8a-4d37-b5c5-9ec5a62eee28');
        $this->assertInstanceOf(Order::class, $order);

        $handler = static::getContainer()->get(GenerateInvoicePdfHandler::class);
        $handler(new GenerateInvoicePdfMessage($order->getId()));

        // Shouldn't have done anything
        $this->assertNull($order->getInvoicePdf());
    }

    public function testConsume()
    {
        self::bootKernel();

        /** @var Order $order */
        $order = static::getContainer()->get(OrderRepository::class)->findOneByUuid('b1e80c11-ca03-4e11-858a-dc00b05c5527');
        $this->assertInstanceOf(Order::class, $order);

        $handler = static::getContainer()->get(GenerateInvoicePdfHandler::class);
        $handler(new GenerateInvoicePdfMessage($order->getId()));

        // Should have created the invoice
        $this->assertNotNull($invoice = $order->getInvoicePdf());
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertTrue($storage->fileExists($invoice->getPathname()));
        $this->assertSame('application/pdf', $storage->mimeType($invoice->getPathname()));

        // Should have sent the email
        $this->assertQueuedEmailCount(1);

        /** @var Email $message */
        $message = $this->getMailerMessage();
        $this->assertSame('[Citipo] Invoice 156 for Acme', $message->getSubject());
        $this->assertSame(
            ['billing@citipo.com'],
            array_map(static fn (Address $a) => $a->getEncodedAddress(), $message->getTo()),
        );
        $this->assertCount(2, $message->getAttachments());
    }
}
