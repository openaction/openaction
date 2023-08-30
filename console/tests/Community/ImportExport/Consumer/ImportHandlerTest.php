<?php

namespace App\Tests\Community\ImportExport\Consumer;

use App\Community\ImportExport\Consumer\ImportHandler;
use App\Community\ImportExport\Consumer\ImportMessage;
use App\Entity\Community\Contact;
use App\Entity\Community\Import;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\ImportRepository;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ImportHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ImportHandler::class);
        $handler(new ImportMessage(0));

        // Shouldn't have done anything
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(0, $messages);
    }

    public function testConsumeAlreadyStarted()
    {
        self::bootKernel();

        /** @var Import $import */
        $import = static::getContainer()->get(ImportRepository::class)->findOneByUuid('5deedfb6-173d-4e8b-b208-f62dbf0c4e80');
        $this->assertInstanceOf(Import::class, $import);

        // Create uploaded file and handle the message
        static::getContainer()->get('cdn.storage')->write('import-started.xlsx', file_get_contents(__DIR__.'/../../../Fixtures/import/contacts-map-columns.xlsx'));

        $handler = static::getContainer()->get(ImportHandler::class);
        $handler(new ImportMessage($import->getId()));
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var Import $import */
        $import = static::getContainer()->get(ImportRepository::class)->findOneByUuid('b25ca589-a613-4e62-ac0b-168b9bdf0339');
        $this->assertInstanceOf(Import::class, $import);

        $job = $import->getJob();
        $this->assertFalse($job->isFinished());
        $this->assertSame(0, $job->getTotal());

        // Create uploaded file and handle the message
        static::getContainer()->get('cdn.storage')->write('import-not-started.xlsx', file_get_contents(__DIR__.'/../../../Fixtures/import/contacts-map-columns.xlsx'));

        $handler = static::getContainer()->get(ImportHandler::class);
        $handler(new ImportMessage($import->getId()));

        // The contact should have been created
        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'fabiennebeaujolie1@jourrapide.com']);

        // Fetch the tags association changes
        static::getContainer()->get(EntityManagerInterface::class)->refresh($contact);

        $this->assertSame(36778547219895752, $contact->getArea()->getId());
        $this->assertSame('Mme', $contact->getProfileFormalTitle());
        $this->assertSame('Fabienne1', $contact->getProfileFirstName());
        $this->assertSame('Miron', $contact->getProfileMiddleName());
        $this->assertSame('Beaujolie', $contact->getProfileLastName());
        $this->assertSame('1961-09-07', $contact->getProfileBirthdate()->format('Y-m-d'));
        $this->assertSame('female', $contact->getProfileGender());
        $this->assertSame('Total Yard Maintenance', $contact->getProfileCompany());
        $this->assertSame('Private detective', $contact->getProfileJobTitle());
        $this->assertSame('+33 3 15 35 41 79', $contact->getContactPhone());
        $this->assertSame('+33 15 35 41 79', $contact->getContactWorkPhone());
        $this->assertSame('https://facebook.com/FabienMiron', $contact->getSocialFacebook());
        $this->assertSame('https://twitter.com/FabienMiron', $contact->getSocialTwitter());
        $this->assertSame('https://linkedin.com/FabienMiron', $contact->getSocialLinkedIn());
        $this->assertSame('FabienMiron', $contact->getSocialTelegram());
        $this->assertSame('03.15.35.41.79', $contact->getSocialWhatsapp());
        $this->assertSame('5, Rue du Limas', $contact->getAddressStreetLine1());
        $this->assertNull($contact->getAddressStreetLine2());
        $this->assertSame('21200', $contact->getAddressZipCode());
        $this->assertSame('BEAUNE', $contact->getAddressCity());
        $this->assertSame('fr', $contact->getAddressCountry()->getCode());
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());
        $this->assertFalse($contact->hasSettingsReceiveSms());
        $this->assertFalse($contact->hasSettingsReceiveCalls());
        $this->assertSame('Custom comment', $contact->getMetadataComment());

        $tags = $contact->getMetadataTagsNames();
        sort($tags);
        $this->assertSame(['Black', 'Blue', 'Green', 'Red'], $tags);

        // Should have been marked as processed
        static::getContainer()->get(EntityManagerInterface::class)->refresh($job);
        $this->assertSame(7, $job->getTotal());
        $this->assertTrue($job->isFinished());

        // Should have published CRM updates
        $transport = static::getContainer()->get('messenger.transport.async_indexing');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(UpdateCrmDocumentsMessage::class, $messages[0]->getMessage());
    }
}
