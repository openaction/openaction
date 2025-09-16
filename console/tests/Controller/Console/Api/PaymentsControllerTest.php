<?php

namespace App\Tests\Controller\Console\Api;

use App\Entity\Community\ContactPayment;
use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Repository\Community\ContactRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
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
}
