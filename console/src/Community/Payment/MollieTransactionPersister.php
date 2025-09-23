<?php

namespace App\Community\Payment;

use App\Api\Model\ContactApiData;
use App\Api\Persister\ContactApiPersister;
use App\Bridge\Mollie\MollieConnectInterface;
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
        private readonly MollieConnectInterface $mollieConnect,
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
        $transactionData = $this->mollieConnect->getTransaction($this->getAccessToken($organization), $transactionId);
        if (!$transactionData) {
            return [];
        }

        $metadata = (array) ($transactionData['metadata'] ?? []);
        $contactPayload = (array) ($metadata['contact'] ?? []);
        $paymentsPayload = (array) ($metadata['payments'] ?? []);
        if (!$paymentsPayload) {
            return [];
        }

        $contact = $this->contactPersister->persist(ContactApiData::createFromPayload($contactPayload), $organization);
        $method = $this->mapMethod((string) ($transactionData['method'] ?? ''));

        $created = [];
        foreach ($paymentsPayload as $paymentData) {
            $typeStr = (string) ($paymentData['type'] ?? '');
            $amount = (array) ($paymentData['amount'] ?? []);
            $currency = strtoupper((string) ($amount['currency'] ?? ''));
            $value = (string) ($amount['value'] ?? '0');
            $netAmount = (int) round(((float) $value) * 100);

            if (!$typeStr || !$currency || $netAmount <= 0) {
                continue;
            }

            $type = ContactPaymentType::from($typeStr);

            // Fetch potentially existing payment for this transaction or create a new one otherwise
            $payment = $contact->getMolliePaymentByTransactionId($transactionId, $type, $netAmount, $currency);

            if (!$payment) {
                $payment = new ContactPayment(
                    contact: $contact,
                    type: $type,
                    netAmount: $netAmount,
                    feesAmount: 0,
                    currency: $currency,
                    provider: ContactPaymentProvider::Mollie,
                    method: $method,
                );

                $created[] = $payment;
            }

            // Update provider details
            $payment->setPaymentProviderDetails(new ContactPaymentMollieDetails($transactionId, $transactionData));

            // Update payment status
            $payment->setCapturedAt(!empty($transactionData['paidAt']) ? new \DateTimeImmutable($transactionData['paidAt']) : null);
            $payment->setFailedAt(!empty($transactionData['failedAt']) ? new \DateTimeImmutable($transactionData['failedAt']) : null);
            $payment->setCanceledAt(!empty($transactionData['canceledAt']) ? new \DateTimeImmutable($transactionData['canceledAt']) : null);

            if (0.0 !== ((float) $transactionData['amountRefunded']['value']) && !$payment->getRefundedAt()) {
                $payment->setRefundedAt(new \DateTimeImmutable());
            }

            // Payer snapshot from metadata contact
            $this->applyPayerSnapshot($payment, $contactPayload);

            // Append membership to the current one if paid
            if (ContactPaymentType::Membership === $type && 'paid' === $transactionData['status'] ?? '') {
                $this->applyMembershipPeriod($payment, $contact);
            }

            // Keep contact payments collection in sync to help idempotency within same request
            $contact->getPayments()->add($payment);
            $this->em->persist($payment);
        }

        // Flush all at once to never comit sync a transaction partially
        $this->em->flush();

        return $created;
    }

    /**
     * Fetch all recent transactions (last 7 days) for this organization and sync them.
     *
     * @return int Number of payments created
     */
    public function syncRecentTransactions(Organization $organization, string $since = '-7 days'): int
    {
        $payments = $this->mollieConnect->listTransactionsSince(
            apiKey: $this->getAccessToken($organization),
            since: (new \DateTimeImmutable('now'))->modify($since),
        );

        $created = 0;
        foreach ($payments as $p) {
            $created += count($this->syncTransaction($organization, (string) ($p['id'] ?? '')));
        }

        return $created;
    }

    private function getAccessToken(Organization $organization): string
    {
        $accessToken = $organization->getMollieConnectAccessToken();
        $refreshToken = $organization->getMollieConnectRefreshToken();

        if (!$accessToken || !$refreshToken) {
            throw new \RuntimeException('Mollie Connect is not configured for this organization.');
        }

        $expiresAt = $organization->getMollieConnectAccessTokenExpiresAt();
        $nowPlus5 = (new \DateTimeImmutable('now'))->modify('+5 minutes');

        if ($expiresAt instanceof \DateTimeInterface && $expiresAt > $nowPlus5) {
            return $accessToken;
        }

        $tokens = $this->mollieConnect->refreshAccessToken($refreshToken);
        $organization->setMollieConnectAccessToken($tokens['access_token'] ?? null);
        $organization->setMollieConnectRefreshToken($tokens['refresh_token'] ?? null);
        $organization->setMollieConnectAccessTokenExpiresAt(
            isset($tokens['expires_in']) && is_numeric($tokens['expires_in'])
                ? (new \DateTimeImmutable('now'))->modify('+'.((int) $tokens['expires_in']).' seconds')
                : null
        );

        $this->em->persist($organization);
        $this->em->flush();

        return (string) $organization->getMollieConnectAccessToken();
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
