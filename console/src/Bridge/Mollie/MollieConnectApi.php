<?php

namespace App\Bridge\Mollie;

use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use Mollie\Api\MollieApiClient;

class MollieConnectApi implements MollieConnectApiInterface
{
    public function __construct(
        private readonly MollieConnectInterface $mollieConnect,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function getPayment(Organization $organization, string $paymentId): ?array
    {
        $client = $this->getAuthorizedClient($organization);

        try {
            $payment = $client->payments->get($paymentId);
        } catch (\Throwable) {
            return null;
        }

        return $this->normalizePayment($payment);
    }

    public function listPaymentsSince(Organization $organization, \DateTimeImmutable $since): array
    {
        $client = $this->getAuthorizedClient($organization);

        $normalized = [];
        try {
            $collection = $client->payments->page(null, 250); // fetch first page (max limit)
        } catch (\Throwable) {
            return $normalized;
        }

        foreach ($collection as $payment) {
            $createdAt = $payment->createdAt ? new \DateTimeImmutable($payment->createdAt) : null;
            if ($createdAt && $createdAt >= $since) {
                $normalized[] = $this->normalizePayment($payment);
            }
        }

        return $normalized;
    }

    private function getAuthorizedClient(Organization $organization): MollieApiClient
    {
        $accessToken = $organization->getMollieConnectAccessToken();
        $refreshToken = $organization->getMollieConnectRefreshToken();

        if (!$accessToken || !$refreshToken) {
            throw new \RuntimeException('Mollie Connect is not configured for this organization.');
        }

        $expiresAt = $organization->getMollieConnectAccessTokenExpiresAt();
        $nowPlus5 = (new \DateTimeImmutable('now'))->modify('+5 minutes');
        if ($expiresAt instanceof \DateTimeInterface && $expiresAt <= $nowPlus5) {
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

            $accessToken = (string) $organization->getMollieConnectAccessToken();
        }

        $client = new MollieApiClient();
        // OAuth: use access token
        $client->setAccessToken($accessToken);

        return $client;
    }

    /**
     * @return array{
     *   id: string,
     *   amount: array{currency:string,value:string},
     *   method?: string|null,
     *   status?: string|null,
     *   createdAt?: string|null,
     *   paidAt?: string|null,
     *   metadata?: array|null,
     * }
     */
    private function normalizePayment(object $payment): array
    {
        $metadata = null;
        if (isset($payment->metadata)) {
            // metadata can be stdClass in SDK; cast to array recursively
            $metadata = json_decode(json_encode($payment->metadata, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        }

        return [
            'id' => (string) ($payment->id ?? ''),
            'amount' => [
                'currency' => (string) ($payment->amount->currency ?? ''),
                'value' => (string) ($payment->amount->value ?? ''),
            ],
            'method' => isset($payment->method) ? (string) $payment->method : null,
            'status' => isset($payment->status) ? (string) $payment->status : null,
            'createdAt' => isset($payment->createdAt) ? (string) $payment->createdAt : null,
            'paidAt' => isset($payment->paidAt) ? (string) $payment->paidAt : null,
            'metadata' => $metadata,
        ];
    }
}
