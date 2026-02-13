<?php

namespace App\Tests\Controller\Console\Api;

use App\Entity\Community\ContactPayment;
use App\Entity\Community\ContactSubscription;
use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Repository\Community\ContactPaymentRepository;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\ContactSubscriptionRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PaymentsControllerTest extends WebTestCase
{
    private const ORGA_UUID = '219025aa-7fe2-4385-ad8f-31f386720d10';

    public function testForbiddenAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/console/api/'.self::ORGA_UUID.'/payments');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testForbiddenOrganizationNotMember(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'ema.anderson@away.com');

        $client->request('GET', '/console/api/'.self::ORGA_UUID.'/payments');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testListAndFilters(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $container = static::getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var OrganizationRepository $orgas */
        $orgas = $container->get(OrganizationRepository::class);
        /** @var ContactRepository $contacts */
        $contacts = $container->get(ContactRepository::class);

        $orga = $orgas->findOneByUuid(self::ORGA_UUID);
        $this->assertNotNull($orga);

        // Pick two contacts in organization
        $orgContacts = $contacts->createQueryBuilder('c')
            ->andWhere('c.organization = :o')
            ->setParameter('o', $orga)
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();

        $this->assertGreaterThanOrEqual(2, count($orgContacts));
        $contactA = $orgContacts[0];
        $contactB = $orgContacts[1];

        // Payments data
        $now = new \DateTimeImmutable('now');

        // A: captured donation, total 1100
        $pA = ContactPayment::createFixture([
            'contact' => $contactA,
            'type' => ContactPaymentType::Donation,
            'netAmount' => 1000,
            'feesAmount' => 100,
            'currency' => 'EUR',
            'paymentProvider' => ContactPaymentProvider::Mollie,
            'paymentMethod' => ContactPaymentMethod::Card,
            'capturedAt' => $now,
        ]);
        $pA->setCreatedAt(new \DateTime('-1 day'));

        // B: refunded membership, total 5000
        $pB = ContactPayment::createFixture([
            'contact' => $contactA,
            'type' => ContactPaymentType::Membership,
            'netAmount' => 5000,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => ContactPaymentProvider::Mollie,
            'paymentMethod' => ContactPaymentMethod::Wire,
            'refundedAt' => $now,
            'membershipStartAt' => $now->modify('-1 year'),
            'membershipEndAt' => $now->modify('-1 day'),
        ]);
        $pB->setCreatedAt(new \DateTime());

        // C: failed donation, total 2000
        $pC = ContactPayment::createFixture([
            'contact' => $contactB,
            'type' => ContactPaymentType::Donation,
            'netAmount' => 2000,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => ContactPaymentProvider::Manual,
            'paymentMethod' => ContactPaymentMethod::Sepa,
            'failedAt' => $now,
        ]);
        $pC->setCreatedAt(new \DateTime('-2 days'));

        // D: pending donation, total 300
        $pD = ContactPayment::createFixture([
            'contact' => $contactB,
            'type' => ContactPaymentType::Donation,
            'netAmount' => 300,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => ContactPaymentProvider::Manual,
            'paymentMethod' => ContactPaymentMethod::Check,
        ]);
        $pD->setCreatedAt(new \DateTime('-10 days'));

        $em->persist($pA);
        $em->persist($pB);
        $em->persist($pC);
        $em->persist($pD);
        $em->flush();

        // Basic list
        $client->request('GET', '/console/api/'.self::ORGA_UUID.'/payments');
        $this->assertResponseIsSuccessful();
        $payload = Json::decode($client->getResponse()->getContent());
        $this->assertArrayHasKey('meta', $payload);
        $this->assertArrayHasKey('pagination', $payload['meta']);
        $this->assertGreaterThanOrEqual(4, (int) $payload['meta']['pagination']['total']);
        $this->assertArrayHasKey('data', $payload);
        $this->assertNotEmpty($payload['data']);

        // Filter: status captured
        $client->request('GET', '/console/api/'.self::ORGA_UUID.'/payments?status=captured&limit=100');
        $this->assertResponseIsSuccessful();
        $data = Json::decode($client->getResponse()->getContent())['data'];
        $this->assertCount(1, array_filter($data, fn ($i) => 'captured' === $i['status']));

        // Filter: method wire
        $client->request('GET', '/console/api/'.self::ORGA_UUID.'/payments?method=Wire&limit=100');
        $this->assertResponseIsSuccessful();
        $data = Json::decode($client->getResponse()->getContent())['data'];
        $this->assertTrue(count($data) >= 1);
        foreach ($data as $row) {
            $this->assertSame('Wire', $row['method']);
        }

        // Filter: type membership
        $client->request('GET', '/console/api/'.self::ORGA_UUID.'/payments?type=Membership&limit=100');
        $this->assertResponseIsSuccessful();
        $data = Json::decode($client->getResponse()->getContent())['data'];
        foreach ($data as $row) {
            $this->assertSame('Membership', $row['type']);
        }

        // Filter: amount range total = 1100
        $client->request('GET', '/console/api/'.self::ORGA_UUID.'/payments?amount_min=1100&amount_max=1100&limit=100');
        $this->assertResponseIsSuccessful();
        $data = Json::decode($client->getResponse()->getContent())['data'];
        foreach ($data as $row) {
            $this->assertSame(1100, $row['totalAmount']);
        }

        // Filter: date range (last 2 days)
        $dateMin = (new \DateTime('-2 days'))->format('Y-m-d');
        $client->request('GET', '/console/api/'.self::ORGA_UUID.'/payments?date_min='.$dateMin.'&limit=100');
        $this->assertResponseIsSuccessful();
        $data = Json::decode($client->getResponse()->getContent())['data'];
        foreach ($data as $row) {
            $this->assertGreaterThanOrEqual(strtotime($dateMin), strtotime($row['date']));
        }
    }

    public function testAddManualMembershipByEmailComputesDates(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $container = static::getContainer();
        $payments = $container->get(ContactPaymentRepository::class);
        $contacts = $container->get(ContactRepository::class);

        // Use a known contact from the organization and create an active membership
        $contact = $contacts->findOneBy(['email' => 'olivie.gregoire@gmail.com']);
        $this->assertNotNull($contact);

        $today = new \DateTimeImmutable('today');
        $prev = ContactPayment::createFixture([
            'contact' => $contact,
            'type' => ContactPaymentType::Membership,
            'netAmount' => 1000,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => ContactPaymentProvider::Manual,
            'paymentMethod' => ContactPaymentMethod::Card,
            'membershipStartAt' => $today->modify('-10 days'),
            'membershipEndAt' => $today->modify('+20 days'),
        ]);
        static::getContainer()->get(EntityManagerInterface::class)->persist($prev);
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $expectedStart = $today->modify('+21 days');
        $expectedEnd = $expectedStart->modify('+1 year');

        $payload = Json::encode([
            'email' => 'olivie.gregoire@gmail.com',
            'type' => 'Membership',
            'netAmount' => 3500,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => 'Manual',
            'paymentMethod' => 'Card',
        ]);

        $client->request('POST', '/console/api/'.self::ORGA_UUID.'/payments', server: [], content: $payload);
        $this->assertResponseIsSuccessful();

        $created = $payments->createQueryBuilder('p')
            ->andWhere('p.contact = :c')->setParameter('c', $contact)
            ->andWhere('p.type = :t')->setParameter('t', ContactPaymentType::Membership)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getSingleResult();

        $this->assertEquals($expectedStart->format('Y-m-d'), $created->getMembershipStartAt()->format('Y-m-d'));
        $this->assertEquals($expectedEnd->format('Y-m-d'), $created->getMembershipEndAt()->format('Y-m-d'));
    }

    public function testAddManualDonationById(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $contacts = static::getContainer()->get(ContactRepository::class);
        $contact = $contacts->findOneBy(['email' => 'olivie.gregoire@gmail.com']);

        $payload = Json::encode([
            'contactId' => Uid::toBase62($contact->getUuid()),
            'type' => 'Donation',
            'netAmount' => 1000,
            'feesAmount' => 100,
            'currency' => 'EUR',
            'paymentProvider' => 'Manual',
            'paymentMethod' => 'Wire',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'payerEmail' => 'john.doe@example.com',
        ]);

        $client->request('POST', '/console/api/'.self::ORGA_UUID.'/payments', server: [], content: $payload);
        $this->assertResponseIsSuccessful();
    }

    public function testScheduleCreatesOnlyFirstPayment(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $container = static::getContainer();
        /** @var ContactRepository $contacts */
        $contacts = $container->get(ContactRepository::class);
        /** @var ContactPaymentRepository $payments */
        $payments = $container->get(ContactPaymentRepository::class);
        /** @var ContactSubscriptionRepository $subscriptions */
        $subscriptions = $container->get(ContactSubscriptionRepository::class);

        $contact = $contacts->findOneBy(['email' => 'olivie.gregoire@gmail.com']);
        $this->assertNotNull($contact);

        $payload = Json::encode([
            'email' => 'olivie.gregoire@gmail.com',
            'type' => 'Membership',
            'netAmount' => 4200,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentMethod' => 'Sepa',
            'intervalInMonths' => 3,
            'startDate' => '2026-01-01',
            'occurrences' => 4,
        ]);

        $client->request('POST', '/console/api/'.self::ORGA_UUID.'/payments/schedule', server: [], content: $payload);
        $this->assertResponseIsSuccessful();

        $response = Json::decode($client->getResponse()->getContent());
        $this->assertArrayHasKey('data', $response);
        $this->assertCount(1, $response['data']);

        /** @var ContactSubscription|null $subscription */
        $subscription = $subscriptions->findActiveByContactTypeMethod($contact, ContactPaymentType::Membership, ContactPaymentMethod::Sepa);
        $this->assertNotNull($subscription);
        $this->assertSame('2026-01-01', $subscription->getStartsAt()->format('Y-m-d'));
        $this->assertSame('2026-10-01', $subscription->getEndsAt()?->format('Y-m-d'));

        $subscriptionPayments = $payments->createQueryBuilder('p')
            ->andWhere('p.subscription = :subscription')
            ->setParameter('subscription', $subscription)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        $this->assertCount(1, $subscriptionPayments);
        $firstPayment = $subscriptionPayments[0];
        $this->assertSame('2026-01-01', $firstPayment->getCreatedAt()->format('Y-m-d'));
        $this->assertSame('2026-01-01', $firstPayment->getMembershipStartAt()?->format('Y-m-d'));
        $this->assertSame('2026-03-31', $firstPayment->getMembershipEndAt()?->format('Y-m-d'));
    }

    public function testRejectNonManualProvider(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $payload = Json::encode([
            'email' => 'troycovillon@teleworm.us',
            'type' => 'Donation',
            'netAmount' => 100,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => 'Mollie',
            'paymentMethod' => 'Card',
        ]);

        $client->request('POST', '/console/api/'.self::ORGA_UUID.'/payments', server: [], content: $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testForbiddenOrganizationNotMemberOnCreate(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'ema.anderson@away.com');

        $payload = Json::encode([
            'email' => 'troycovillon@teleworm.us',
            'type' => 'Donation',
            'netAmount' => 100,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => 'Manual',
            'paymentMethod' => 'Card',
        ]);

        $client->request('POST', '/console/api/'.self::ORGA_UUID.'/payments', server: [], content: $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
