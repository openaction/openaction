<?php

namespace App\Tests\Stubs;

use App\Bridge\Mollie\MollieConnectApiInterface;
use App\Entity\Organization;

class MockMollieConnectApi implements MollieConnectApiInterface
{
    /** @var array<string, array> */
    private array $payments = [];

    public function seed(array $payments): void
    {
        foreach ($payments as $p) {
            $id = (string) ($p['id'] ?? '');
            if ($id) {
                $this->payments[$id] = $p;
            }
        }
    }

    public function getPayment(Organization $organization, string $paymentId): ?array
    {
        return $this->payments[$paymentId] ?? null;
    }

    public function listPaymentsSince(Organization $organization, \DateTimeImmutable $since): array
    {
        $res = [];
        foreach ($this->payments as $p) {
            $createdAt = isset($p['createdAt']) ? new \DateTimeImmutable((string) $p['createdAt']) : null;
            if ($createdAt && $createdAt >= $since) {
                $res[] = $p;
            }
        }

        return $res;
    }
}
