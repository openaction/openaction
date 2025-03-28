<?php

namespace App\Tests\Community\ImportExport\Consumer;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Community\ImportExport\Consumer\ImportHandler;
use App\Community\ImportExport\Consumer\ImportMessage;
use App\DataFixtures\TestFixtures;
use App\Entity\Community\Contact;
use App\Entity\Community\Import;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\ImportRepository;
use App\Tests\KernelTestCase;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @group without-transaction
 */
class ImportHandlerTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        // Do not use dmaicher/doctrine-test-bundle as the database structure changes need to be persisted
        // for the indexing dumping to work
        StaticDriver::setKeepStaticConnections(false);
    }

    public static function tearDownAfterClass(): void
    {
        // Reload fixtures for other tests
        $loader = new Loader();
        $loader->addFixture(new TestFixtures(static::getContainer()->get(UserPasswordHasherInterface::class)));

        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);

        $executor = new ORMExecutor(static::getContainer()->get(EntityManagerInterface::class), $purger);
        $executor->execute($loader->getFixtures());

        // Restore dmaicher/doctrine-test-bundle for other tests
        StaticDriver::setKeepStaticConnections(true);
    }

    public function testConsumeValid(): void
    {
        self::bootKernel();

        /** @var Import $import */
        $import = static::getContainer()->get(ImportRepository::class)->findOneByUuid('b25ca589-a613-4e62-ac0b-168b9bdf0339');
        $this->assertInstanceOf(Import::class, $import);

        $job = $import->getJob();
        $this->assertFalse($job->isFinished());
        $this->assertSame(12, $job->getTotal());

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
        $this->assertSame('+33 3 15 35 41 79', $contact->getContactWorkPhone());
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
        $this->assertSame(['Black', 'Blue', 'Red'], $tags);

        // Should have been marked as processed
        $job = $import->getJob();
        static::getContainer()->get(EntityManagerInterface::class)->refresh($job);
        $this->assertTrue($job->isFinished());

        // Should have published stats update
        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(RefreshContactStatsMessage::class, $messages[0]->getMessage());
    }
}
