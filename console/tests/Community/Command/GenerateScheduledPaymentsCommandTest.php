<?php

namespace App\Tests\Community\Command;

use App\Command\Community\GenerateScheduledPaymentsCommand;
use App\Entity\Community\ContactPayment;
use App\Entity\Community\ContactSubscription;
use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Repository\Community\ContactPaymentRepository;
use App\Repository\Community\ContactRepository;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateScheduledPaymentsCommandTest extends KernelTestCase
{
    public function testGeneratesNextPaymentWhenLatestIsDue(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        /** @var ContactRepository $contacts */
        $contacts = static::getContainer()->get(ContactRepository::class);
        /** @var ContactPaymentRepository $payments */
        $payments = static::getContainer()->get(ContactPaymentRepository::class);

        $contact = $contacts->findOneBy(['email' => 'olivie.gregoire@gmail.com']);
        $this->assertNotNull($contact);

        $subscription = ContactSubscription::createFixture([
            'contact' => $contact,
            'type' => ContactPaymentType::Donation,
            'netAmount' => 1200,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentMethod' => ContactPaymentMethod::Sepa,
            'intervalInMonths' => 1,
            'startsAt' => new \DateTimeImmutable('-3 months'),
            'endsAt' => new \DateTimeImmutable('+6 months'),
        ]);
        $em->persist($subscription);

        $latest = $subscription->createPaymentForDate((new \DateTimeImmutable('today'))->modify('-1 month'));
        $em->persist($latest);
        $em->flush();

        $command = static::getContainer()->get(GenerateScheduledPaymentsCommand::class);
        $tester = new CommandTester($command);
        $tester->execute([]);

        $subscriptionPayments = $payments->createQueryBuilder('p')
            ->andWhere('p.subscription = :subscription')
            ->setParameter('subscription', $subscription)
            ->orderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        $this->assertCount(2, $subscriptionPayments);

        /** @var ContactPayment $generated */
        $generated = end($subscriptionPayments);
        $this->assertSame((new \DateTimeImmutable('today'))->format('Y-m-d'), $generated->getCreatedAt()->format('Y-m-d'));
        $this->assertSame(ContactPaymentProvider::Manual, $generated->getPaymentProvider());
        $this->assertSame(ContactPaymentMethod::Sepa, $generated->getPaymentMethod());
        $this->assertNull($generated->getCapturedAt());
        $this->assertNull($generated->getFailedAt());
        $this->assertNull($generated->getCanceledAt());
        $this->assertNull($generated->getRefundedAt());
    }

    public function testDoesNotGeneratePaymentEarly(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        /** @var ContactRepository $contacts */
        $contacts = static::getContainer()->get(ContactRepository::class);
        /** @var ContactPaymentRepository $payments */
        $payments = static::getContainer()->get(ContactPaymentRepository::class);

        $contact = $contacts->findOneBy(['email' => 'olivie.gregoire@gmail.com']);
        $this->assertNotNull($contact);

        $subscription = ContactSubscription::createFixture([
            'contact' => $contact,
            'type' => ContactPaymentType::Donation,
            'netAmount' => 1200,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentMethod' => ContactPaymentMethod::Sepa,
            'intervalInMonths' => 1,
            'startsAt' => new \DateTimeImmutable('tomorrow'),
            'endsAt' => new \DateTimeImmutable('+6 months'),
        ]);
        $em->persist($subscription);

        $latest = ContactPayment::createFixture([
            'contact' => $contact,
            'subscription' => $subscription,
            'type' => ContactPaymentType::Donation,
            'netAmount' => 1200,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentProvider' => ContactPaymentProvider::Manual,
            'paymentMethod' => ContactPaymentMethod::Sepa,
            'capturedAt' => new \DateTimeImmutable('yesterday'),
        ]);
        $latest->setCreatedAt(new \DateTime('tomorrow'));

        $em->persist($latest);
        $em->flush();

        $command = static::getContainer()->get(GenerateScheduledPaymentsCommand::class);
        $tester = new CommandTester($command);
        $tester->execute([]);

        $count = $payments->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.subscription = :subscription')
            ->setParameter('subscription', $subscription)
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(1, (int) $count);
    }

    public function testGenerationIsIdempotentForSameDueDate(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        /** @var ContactRepository $contacts */
        $contacts = static::getContainer()->get(ContactRepository::class);
        /** @var ContactPaymentRepository $payments */
        $payments = static::getContainer()->get(ContactPaymentRepository::class);

        $contact = $contacts->findOneBy(['email' => 'olivie.gregoire@gmail.com']);
        $this->assertNotNull($contact);

        $subscription = ContactSubscription::createFixture([
            'contact' => $contact,
            'type' => ContactPaymentType::Donation,
            'netAmount' => 1200,
            'feesAmount' => 0,
            'currency' => 'EUR',
            'paymentMethod' => ContactPaymentMethod::Sepa,
            'intervalInMonths' => 1,
            'startsAt' => new \DateTimeImmutable('-2 months'),
            'endsAt' => new \DateTimeImmutable('+6 months'),
        ]);
        $em->persist($subscription);

        $latestDue = $subscription->createPaymentForDate(new \DateTimeImmutable('today'));
        $alreadyGenerated = $subscription->createPaymentForDate(new \DateTimeImmutable('tomorrow'));
        $em->persist($latestDue);
        $em->persist($alreadyGenerated);
        $em->flush();

        $command = static::getContainer()->get(GenerateScheduledPaymentsCommand::class);
        $tester = new CommandTester($command);
        $tester->execute([]);
        $tester->execute([]);

        $count = $payments->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.subscription = :subscription')
            ->setParameter('subscription', $subscription)
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(2, (int) $count);
    }
}
