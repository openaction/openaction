<?php

namespace App\Bridge\Mollie;

use App\Entity\Organization;

/**
 * Small bridge around Mollie OAuth (Connect) to fetch payments for organizations.
 * Returns lightweight associative arrays so tests can mock without Mollie SDK.
 */
interface MollieConnectApiInterface
{
    /**
     * Fetch a single payment by id for the given organization.
     *
     * @return array|null A normalized payment array or null if not found
     */
    public function getPayment(Organization $organization, string $paymentId): ?array;

    /**
     * List payments created since the provided date (inclusive).
     *
     * @return array<int, array> List of normalized payments
     */
    public function listPaymentsSince(Organization $organization, \DateTimeImmutable $since): array;
}
