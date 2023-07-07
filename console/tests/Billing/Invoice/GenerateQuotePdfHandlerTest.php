<?php

namespace App\Tests\Billing\Invoice;

use App\Billing\Invoice\GenerateQuotePdfHandler;
use App\Billing\Invoice\GenerateQuotePdfMessage;
use App\Entity\Billing\Quote;
use App\Repository\Billing\QuoteRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class GenerateQuotePdfHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(GenerateQuotePdfHandler::class);
        $handler(new GenerateQuotePdfMessage(0));

        // Shouldn't have done anything
        $this->assertQueuedEmailCount(0);
    }

    public function testConsume()
    {
        self::bootKernel();

        /** @var Quote $quote */
        $quote = static::getContainer()->get(QuoteRepository::class)->findOneByUuid('c5f0ebfa-625c-4b57-9d2c-6e643fe1d973');
        $this->assertInstanceOf(Quote::class, $quote);

        $handler = static::getContainer()->get(GenerateQuotePdfHandler::class);
        $handler(new GenerateQuotePdfMessage($quote->getId()));

        // Should have created the invoice
        $this->assertNotNull($pdf = $quote->getPdf());
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertTrue($storage->fileExists($pdf->getPathname()));
        $this->assertSame('application/pdf', $storage->mimeType($pdf->getPathname()));

        // Should have sent the email
        $this->assertQueuedEmailCount(1);

        /** @var Email $message */
        $message = $this->getMailerMessage();
        $this->assertSame('[Citipo] Quote 1 for Acme', $message->getSubject());
        $this->assertSame(
            ['billing@citipo.com'],
            array_map(static fn (Address $a) => $a->getEncodedAddress(), $message->getTo()),
        );
        $this->assertCount(2, $message->getAttachments());
    }
}
