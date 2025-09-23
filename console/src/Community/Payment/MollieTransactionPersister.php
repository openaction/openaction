<?php

namespace App\Community\Payment;

use App\Api\Model\ContactApiData;
use App\Api\Persister\ContactApiPersister;
use App\Bridge\Mollie\MollieConnectApiInterface;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactPayment;
use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Entity\Community\Model\ContactPaymentMollieDetails;
use App\Entity\Organization;
use App\Repository\Community\ContactPaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

class MollieTransactionPersister
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ContactApiPersister $contactPersister,
        private readonly ContactPaymentRepository $payments,
        private readonly MollieConnectApiInterface $mollieConnectApi,
    ) {
    }

    /**
     * Sync a single Mollie payment by id for the given organization.
     * Returns the created payments (can be multiple for a single transaction metadata).
     *
     * @return ContactPayment[]
     */
    public function syncTransaction(Organization $organization, string $transactionId): array
    {
        $payment = $this->mollieConnectApi->getPayment($organization, $transactionId);
        if (!$payment) {
            return [];
        }

        $metadata = (array) ($payment['metadata'] ?? []);
        $contactPayload = (array) ($metadata['contact'] ?? []);
        $paymentsPayload = (array) ($metadata['payments'] ?? []);

        if (!$paymentsPayload) {
            return [];
        }

        $contact = $this->persistContact($organization, $contactPayload);

        $created = [];
        foreach ($paymentsPayload as $p) {
            $typeStr = (string) ($p['type'] ?? '');
            $amount = (array) ($p['amount'] ?? []);
            $currency = strtoupper((string) ($amount['currency'] ?? ''));
            $value = (string) ($amount['value'] ?? '0');
            $netAmount = (int) round(((float) $value) * 100);

            if (!$typeStr || !$currency || $netAmount <= 0) {
                continue;
            }

            // Skip duplicates
            if ($this->hasDuplicate($contact, $transactionId, $typeStr, $netAmount, $currency)) {
                continue;
            }

            $type = ContactPaymentType::from($typeStr);
            $method = $this->mapMethod((string) ($payment['method'] ?? ''));

            $entity = new ContactPayment(
                $contact,
                $type,
                $netAmount,
                0,
                $currency,
                ContactPaymentProvider::Mollie,
                $method
            );

            // Payer snapshot from metadata contact
            $this->applyPayerSnapshot($entity, $contactPayload);

            // Membership period
            if (ContactPaymentType::Membership === $type) {
                $this->applyMembershipPeriod($entity, $contact);
            }

            // Capture time if paid
            $status = (string) ($payment['status'] ?? '');
            if ('paid' === $status && !empty($payment['paidAt'])) {
                try {
                    $entity->setMembershipPeriod($entity->getMembershipStartAt(), $entity->getMembershipEndAt()); // keep as-is
                } catch (\Throwable) {
                }
            }

            // Provider details
            $entity->setPaymentProviderDetails(new ContactPaymentMollieDetails($transactionId, $payment));

            $this->em->persist($entity);
            // Keep contact payments collection in sync to help idempotency within same request
            $contact->getPayments()->add($entity);
            $created[] = $entity;
        }

        if ($created) {
            $this->em->flush();
        }

        return $created;
    }

    /**
     * Fetch all recent transactions (last 7 days) for this organization and sync them.
     *
     * @return int Number of payments created
     */
    public function syncRecentTransactions(Organization $organization): int
    {
        $since = (new \DateTimeImmutable('now'))->modify('-7 days');
        $payments = $this->mollieConnectApi->listPaymentsSince($organization, $since);

        $created = 0;
        foreach ($payments as $p) {
            $created += count($this->syncTransaction($organization, (string) ($p['id'] ?? '')));
        }

        return $created;
    }

    private function persistContact(Organization $organization, array $payload): Contact
    {
        $data = ContactApiData::createFromPayload($payload);

        return $this->contactPersister->persist($data, $organization);
    }

    private function hasDuplicate(Contact $contact, string $transactionId, string $type, int $amount, string $currency): bool
    {
        foreach ($contact->getPayments() as $existing) {
            if (
                ContactPaymentProvider::Mollie === $existing->getPaymentProvider()
                && $existing->getType()->value === $type
                && $existing->getNetAmount() === $amount
                && strtoupper($existing->getCurrency()) === strtoupper($currency)
            ) {
                $details = null;
                try {
                    $ref = new \ReflectionProperty($existing, 'paymentProviderDetails');
                    $ref->setAccessible(true);
                    $details = $ref->getValue($existing);
                } catch (\Throwable) {
                }

                if ($details instanceof ContactPaymentMollieDetails && $details->transactionId === $transactionId) {
                    return true;
                }
            }
        }

        return false;
    }

    private function applyPayerSnapshot(ContactPayment $payment, array $contactPayload): void
    {
        $birthdate = null;
        if (!empty($contactPayload['profileBirthdate'])) {
            try {
                $birthdate = new \DateTime((string) $contactPayload['profileBirthdate']);
            } catch (\Throwable) {
                $birthdate = null;
            }
        }

        $payment->setPayerSnapshot(
            $contactPayload['profileFormalTitle'] ?? null,
            $contactPayload['profileFirstName'] ?? null,
            $contactPayload['profileLastName'] ?? null,
            $contactPayload['email'] ?? null,
            $contactPayload['addressStreetLine1'] ?? null,
            $contactPayload['addressStreetLine2'] ?? null,
            $contactPayload['addressCity'] ?? null,
            $contactPayload['addressZipCode'] ?? null,
            $contactPayload['addressCountry'] ?? null,
            $birthdate,
            $contactPayload['contactPhone'] ?? null,
            $contactPayload['profileNationality'] ?? null,
            $contactPayload['fiscalCountryCode'] ?? null,
        );
    }

    private function applyMembershipPeriod(ContactPayment $payment, Contact $contact): void
    {
        $today = new \DateTimeImmutable('today');

        $qb = $this->payments->createQueryBuilder('p')
            ->andWhere('p.contact = :contact')
            ->setParameter('contact', $contact)
            ->andWhere('p.type = :type')
            ->setParameter('type', ContactPaymentType::Membership)
            ->andWhere('p.membershipStartAt <= :now')
            ->andWhere('p.membershipEndAt >= :now')
            ->setParameter('now', $today)
            ->orderBy('p.membershipEndAt', 'DESC')
            ->setMaxResults(1);

        $active = $qb->getQuery()->getOneOrNullResult();

        if ($active instanceof ContactPayment) {
            $start = $active->getMembershipEndAt()?->modify('+1 day') ?? $today;
        } else {
            $start = $today;
        }

        $end = $start->modify('+1 year');
        $payment->setMembershipPeriod($start, $end);
    }

    private function mapMethod(string $mollieMethod): ContactPaymentMethod
    {
        return match ($mollieMethod) {
            'creditcard' => ContactPaymentMethod::Card,
            'banktransfer' => ContactPaymentMethod::Wire,
            'directdebit' => ContactPaymentMethod::Sepa,
            default => ContactPaymentMethod::Other,
        };
    }
}
