<?php

namespace App\Tests\Controller\Api\Community;

use App\DataFixtures\TestFixtures;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactPayment;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Repository\Community\ContactPaymentRepository;
use App\Repository\Community\ContactRepository;
use App\Tests\ApiTestCase;
use App\Util\Json;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @group without-transaction
 */
class ContactPaymentControllerTest extends ApiTestCase
{
    private function reloadFixtures(): void
    {
        StaticDriver::setKeepStaticConnections(false);
        $loader = new Loader();
        $loader->addFixture(new TestFixtures(static::getContainer()->get(UserPasswordHasherInterface::class)));
        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor = new ORMExecutor(static::getContainer()->get(EntityManagerInterface::class), $purger);
        $executor->execute($loader->getFixtures());
        StaticDriver::setKeepStaticConnections(true);
    }

    public function testAddPaymentUnauthorized(): void
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/contacts/payments', null, 401);
    }

    public function testAddMembershipPaymentByEmailComputesDates(): void
    {
        $client = self::createClient();
        $this->reloadFixtures();

        /** @var ContactRepository $contacts */
        $contacts = static::getContainer()->get(ContactRepository::class);
        /** @var Contact $contact */
        $contact = $contacts->findOneBy(['email' => 'troycovillon@teleworm.us']);

        // Sanity
        $this->assertInstanceOf(Contact::class, $contact);

        /** @var ContactPaymentRepository $payments */
        $payments = static::getContainer()->get(ContactPaymentRepository::class);

        // Previous active membership end
        $prev = $payments->createQueryBuilder('p')
            ->andWhere('p.contact = :c')
            ->setParameter('c', $contact)
            ->andWhere('p.type = :t')
            ->setParameter('t', ContactPaymentType::Membership)
            ->orderBy('p.membershipEndAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertInstanceOf(ContactPayment::class, $prev);
        $expectedStart = $prev->getMembershipEndAt()->modify('+1 day');
        $expectedEnd = $expectedStart->modify('+1 year');

        $content = Json::encode([
            'email' => 'troycovillon@teleworm.us',
            'type' => 'Membership',
            'netAmount' => 3500,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => 'Mollie',
            'paymentMethod' => 'Card',
            'paymentProviderDetails' => [
                'transactionId' => 'tr_test_123',
                'rawPayload' => ['foo' => 'bar'],
            ],
        ]);

        $this->apiRequest($client, 'POST', '/api/community/contacts/payments', self::ACME_TOKEN, Response::HTTP_OK, $content);

        // Fetch last membership
        /** @var ContactPayment $created */
        $created = $payments->createQueryBuilder('p')
            ->andWhere('p.contact = :c')
            ->setParameter('c', $contact)
            ->andWhere('p.type = :t')
            ->setParameter('t', ContactPaymentType::Membership)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedStart->format('Y-m-d'), $created->getMembershipStartAt()->format('Y-m-d'));
        $this->assertEquals($expectedEnd->format('Y-m-d'), $created->getMembershipEndAt()->format('Y-m-d'));
    }

    public function testAddDonationPaymentById(): void
    {
        $client = self::createClient();
        $this->reloadFixtures();

        /** @var ContactRepository $contacts */
        $contacts = static::getContainer()->get(ContactRepository::class);
        /** @var Contact $contact */
        $contact = $contacts->findOneBy(['email' => 'olivie.gregoire@gmail.com']);

        $payload = Json::encode([
            'contactId' => \App\Util\Uid::toBase62($contact->getUuid()),
            'type' => 'Donation',
            'netAmount' => 1000,
            'feesAmount' => 100,
            'currency' => 'EUR',
            'paymentProvider' => 'Mollie',
            'paymentMethod' => 'Card',
            'paymentProviderDetails' => [
                'transactionId' => 'tr_test_456',
                'rawPayload' => ['baz' => 'qux'],
            ],
            'firstName' => 'John',
            'lastName' => 'Doe',
            'payerEmail' => 'john.doe@example.com',
        ]);

        $this->apiRequest($client, 'POST', '/api/community/contacts/payments', self::CITIPO_TOKEN, Response::HTTP_OK, $payload);
        $this->assertTrue(true); // No exception means OK
    }
}
