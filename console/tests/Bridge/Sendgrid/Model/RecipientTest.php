<?php

namespace App\Tests\Bridge\Sendgrid\Model;

use App\Bridge\Sendgrid\Model\Recipient;
use App\Entity\Community\Contact;
use App\Repository\Community\ContactRepository;
use App\Tests\KernelTestCase;

class RecipientTest extends KernelTestCase
{
    public function testCreateFromContactMergesAdditionalVariablesAndLegacyAliases(): void
    {
        self::bootKernel();

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneByUuid('20e51b91-bdec-495d-854d-85d6e74fc75e');
        $this->assertInstanceOf(Contact::class, $contact);

        $recipient = Recipient::createFromContact($contact, 'message-id', [
            '-payment-currency-' => 'EUR',
        ]);

        $vars = $recipient->getVariables();
        self::assertSame('EUR', $vars['-payment-currency-'] ?? null);
        self::assertSame($contact->getEmail(), $recipient->getEmail());
        self::assertArrayHasKey('-contact-email-', $vars);
        self::assertArrayHasKey('-contact-formal-title-', $vars);
        self::assertArrayHasKey('-contact-formaltitle-', $vars);
        self::assertSame($vars['-contact-formal-title-'], $vars['-contact-formaltitle-']);
        self::assertArrayHasKey('-contact-job-title-', $vars);
        self::assertArrayHasKey('-contact-jobtitle-', $vars);
        self::assertSame($vars['-contact-job-title-'], $vars['-contact-jobtitle-']);
        self::assertArrayHasKey('-contact-streetline-1-', $vars);
        self::assertArrayHasKey('-contact-streetline1-', $vars);
        self::assertSame($vars['-contact-streetline-1-'], $vars['-contact-streetline1-']);
        self::assertArrayHasKey('-contact-streetline-2-', $vars);
        self::assertArrayHasKey('-contact-streetline2-', $vars);
        self::assertSame($vars['-contact-streetline-2-'], $vars['-contact-streetline2-']);
    }
}
