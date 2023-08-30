<?php

namespace App\Tests\Bridge\Revue\Consumer;

use App\Bridge\Revue\Consumer\RevueSyncHandler;
use App\Bridge\Revue\Consumer\RevueSyncMessage;
use App\Entity\Community\Contact;
use App\Entity\Integration\RevueAccount;
use App\Repository\Community\ContactRepository;
use App\Repository\Integration\RevueAccountRepository;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class RevueSyncHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(RevueSyncHandler::class);
        $handler(new RevueSyncMessage(0));

        // Shouldn't have done anything
        $this->assertNull(static::getContainer()->get(ContactRepository::class)->findOneBy([
            'email' => 'revue.subscriber@gmail.com',
        ]));
    }

    public function testConsumeDisabled()
    {
        self::bootKernel();

        /** @var RevueAccount $account */
        $account = static::getContainer()->get(RevueAccountRepository::class)->findOneBy(['label' => 'titouangalopin']);
        $this->assertInstanceOf(RevueAccount::class, $account);

        $handler = static::getContainer()->get(RevueSyncHandler::class);
        $handler(new RevueSyncMessage($account->getId()));

        // Shouldn't have done anything
        $this->assertNull(static::getContainer()->get(ContactRepository::class)->findOneBy([
            'email' => 'revue.subscriber@gmail.com',
        ]));

        // Shouldn't have synced
        $account = static::getContainer()->get(RevueAccountRepository::class)->find($account->getId());
        $this->assertSame('2021-09-26 11:30:00', $account->getLastSync()->format('Y-m-d H:i:s'));
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var RevueAccount $account */
        $account = static::getContainer()->get(RevueAccountRepository::class)->findOneBy(['label' => 'citipoapp']);
        $this->assertInstanceOf(RevueAccount::class, $account);

        $handler = static::getContainer()->get(RevueSyncHandler::class);
        $handler(new RevueSyncMessage($account->getId()));

        // Should have created a contact
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy([
            'email' => 'revue.subscriber@gmail.com',
        ]);

        // Fetch the tags association changes
        static::getContainer()->get(EntityManagerInterface::class)->refresh($contact);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame('Acme', $contact->getOrganization()->getName());
        $this->assertSame('Revue', $contact->getProfileFirstName());
        $this->assertSame('Subscriber', $contact->getProfileLastName());
        $this->assertSame(['TwitterRevue'], $contact->getMetadataTagsNames());
        $this->assertSame('revue:311518323', $contact->getMetadataSource());

        // Should have synced
        $account = static::getContainer()->get(RevueAccountRepository::class)->find($account->getId());
        $this->assertGreaterThan(new \DateTime('30 seconds ago'), $account->getLastSync());
    }
}
