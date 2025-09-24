<?php

namespace App\Tests\Community\Payment;

use App\Bridge\Mollie\MollieConnectApiInterface;
use App\Community\Payment\MollieTransactionPersister;
use App\Entity\Community\Contact;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Entity\Organization;
use App\Repository\Community\ContactRepository;
use App\Repository\OrganizationRepository;
use App\Tests\KernelTestCase;

class MollieTransactionPersisterTest extends KernelTestCase
{
    public function testSyncSingleTransactionAndIgnoreDuplicate(): void
    {
        static::bootKernel();

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['name' => 'Example Co']);
        $orga->setMollieConnectAccessToken('test_EFQB5UmyNQxbzUVwSQFrp2Jj47KHsv');
        $orga->setMollieConnectRefreshToken('refresh');
        $orga->setMollieConnectAccessTokenExpiresAt((new \DateTimeImmutable('now'))->modify('+30 minutes'));

        $em = self::getContainer()->get('doctrine')->getManager();
        $em->persist($orga);
        $em->flush();

        /** @var MollieTransactionPersister $persister */
        $persister = self::getContainer()->get(MollieTransactionPersister::class);
        $persister->syncTransaction($orga, 'tr_p3JfBgnNu7GpsiHo6GaEJ');

        /** @var Contact $contact */
        $contact = self::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'titouan.galopin@citipo.com']);

        dd($contact, $contact->getPayments()->toArray());

        // Test key: test_EFQB5UmyNQxbzUVwSQFrp2Jj47KHsv


        $container = static::getContainer();
        /** @var OrganizationRepository $orgas */
        $orgas = $container->get(OrganizationRepository::class);
        /** @var Organization $orga */
        $orga = $orgas->findOneBy(['name' => 'Example Co']);
        $orga->setMollieConnectAccessToken('access');
        $orga->setMollieConnectRefreshToken('refresh');
        $orga->setMollieConnectAccessTokenExpiresAt((new \DateTimeImmutable('now'))->modify('+30 minutes'));
        $em = $container->get('doctrine')->getManager();
        $em->persist($orga);
        $em->flush();

        /** @var MollieConnectApiInterface $bridge */
        $bridge = $container->get(MollieConnectApiInterface::class);
        \assert(method_exists($bridge, 'seed'));
        $metadataContact = [
            'email' => 'donor@example.org',
            'profileFormalTitle' => 'Mr',
            'profileFirstName' => 'John',
            'profileLastName' => 'Doe',
            'profileNationality' => 'FR',
            'contactPhone' => '0600000000',
            'addressStreetLine1' => '1 rue de Paris',
            'addressZipCode' => '75001',
            'addressCity' => 'Paris',
            'addressCountry' => 'FR',
            'settingsReceiveNewsletters' => true,
            'settingsReceiveSms' => false,
            'settingsReceiveCalls' => false,
        ];
        $bridge->seed([
            [
                'id' => 'tr_123',
                'amount' => ['currency' => 'EUR', 'value' => '25.00'],
                'method' => 'creditcard',
                'status' => 'paid',
                'createdAt' => (new \DateTimeImmutable('-1 day'))->format(DATE_ATOM),
                'paidAt' => (new \DateTimeImmutable('-1 day'))->format(DATE_ATOM),
                'metadata' => [
                    'contact' => $metadataContact,
                    'payments' => [
                        [
                            'type' => 'Donation',
                            'amount' => ['currency' => 'EUR', 'value' => '25.00'],
                        ],
                    ],
                ],
            ],
        ]);

        /** @var MollieTransactionPersister $service */
        $service = $container->get(MollieTransactionPersister::class);
        $created = $service->syncTransaction($orga, 'tr_123');
        $this->assertCount(1, $created);
        $payment = $created[0];
        $this->assertSame(ContactPaymentType::Donation, $payment->getType());
        $this->assertSame(2500, $payment->getNetAmount());
        $this->assertSame('EUR', $payment->getCurrency());
        $this->assertSame(ContactPaymentProvider::Mollie, $payment->getPaymentProvider());

        // Idempotency
        $createdAgain = $service->syncTransaction($orga, 'tr_123');
        $this->assertCount(0, $createdAgain);

        // No extra duplicates created in DB
    }

    public function testSyncRecentTransactions(): void
    {
        self::ensureKernelShutdown();
        static::bootKernel();

        $container = static::getContainer();
        /** @var OrganizationRepository $orgas */
        $orgas = $container->get(OrganizationRepository::class);
        /** @var Organization $orga */
        $orga = $orgas->findOneBy(['name' => 'Example Co']);
        $orga->setMollieConnectAccessToken('access');
        $orga->setMollieConnectRefreshToken('refresh');
        $orga->setMollieConnectAccessTokenExpiresAt((new \DateTimeImmutable('now'))->modify('+30 minutes'));
        $em = $container->get('doctrine')->getManager();
        $em->persist($orga);
        $em->flush();

        /** @var MollieConnectApiInterface $bridge */
        $bridge = $container->get(MollieConnectApiInterface::class);
        \assert(method_exists($bridge, 'seed'));
        $commonMeta = [
            'contact' => [
                'email' => 'member@example.org',
                'profileFirstName' => 'Alice',
                'profileLastName' => 'Smith',
            ],
        ];
        $bridge->seed([
            [
                'id' => 'tr_old',
                'amount' => ['currency' => 'EUR', 'value' => '10.00'],
                'method' => 'banktransfer',
                'status' => 'paid',
                'createdAt' => (new \DateTimeImmutable('-8 days'))->format(DATE_ATOM),
                'paidAt' => (new \DateTimeImmutable('-8 days'))->format(DATE_ATOM),
                'metadata' => $commonMeta + [
                    'payments' => [
                        [
                            'type' => 'Donation',
                            'amount' => ['currency' => 'EUR', 'value' => '10.00'],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'tr_recent',
                'amount' => ['currency' => 'EUR', 'value' => '20.00'],
                'method' => 'directdebit',
                'status' => 'paid',
                'createdAt' => (new \DateTimeImmutable('-2 days'))->format(DATE_ATOM),
                'paidAt' => (new \DateTimeImmutable('-2 days'))->format(DATE_ATOM),
                'metadata' => $commonMeta + [
                    'payments' => [
                        [
                            'type' => 'Membership',
                            'amount' => ['currency' => 'EUR', 'value' => '20.00'],
                        ],
                    ],
                ],
            ],
        ]);

        /** @var MollieTransactionPersister $service */
        $service = $container->get(MollieTransactionPersister::class);
        $createdCount = $service->syncRecentTransactions($orga);
        $this->assertSame(1, $createdCount); // only the recent one
    }
}
