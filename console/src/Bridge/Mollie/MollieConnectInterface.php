<?php

namespace App\Bridge\Mollie;

use App\Entity\Organization;

interface MollieConnectInterface
{
    public function getAuthorizationUrl(string $state, array $scopes): string;

    /**
     * @return array{access_token:string, refresh_token:string, expires_in?:int, token_type?:string, scope?:string}
     */
    public function exchangeCodeForTokens(string $code): array;

    /**
     * @return array{access_token:string, refresh_token:string, expires_in?:int, token_type?:string, scope?:string}
     */
    public function refreshAccessToken(string $refreshToken): array;

    /**
     * Fetch a single payment by id for the given organization.
     *
     * @return array|null A normalized payment array or null if not found
     */
    public function getTransaction(string $apiKey, string $paymentId): ?array;

    /**
     * List payments created since the provided date (inclusive).
     *
     * @return array<int, array> List of normalized payments
     */
    public function listTransactionsSince(string $apiKey, \DateTimeImmutable $since): array;
}
