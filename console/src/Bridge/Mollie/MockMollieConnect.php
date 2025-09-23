<?php

namespace App\Bridge\Mollie;

use App\Entity\Organization;

class MockMollieConnect implements MollieConnectInterface
{
    /** @var array<string, array> */
    private array $payments = [];

    public function __construct(
        private readonly string $mollieConnectClientId,
        private readonly string $mollieConnectClientSecret = '',
        private readonly string $mollieConnectRedirectUri = '',
    ) {
    }

    public function getAuthorizationUrl(string $state, array $scopes): string
    {
        $params = [
            'client_id' => $this->mollieConnectClientId,
            'redirect_uri' => $this->mollieConnectRedirectUri,
            'state' => $state,
            'scope' => implode(' ', $scopes),
            'response_type' => 'code',
            'approval_prompt' => 'auto',
        ];

        return 'https://my.mollie.com/oauth2/authorize?'.http_build_query($params);
    }

    public function exchangeCodeForTokens(string $code): array
    {
        return [
            'access_token' => 'access_123',
            'refresh_token' => 'refresh_456',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'scope' => 'payments.read organizations.read',
        ];
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        return [
            'access_token' => 'refreshed_access',
            'refresh_token' => 'refreshed_refresh',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'scope' => 'payments.read organizations.read',
        ];
    }

    public function seed(array $payments): void
    {
        foreach ($payments as $p) {
            $id = (string) ($p['id'] ?? '');
            if ($id) {
                $this->payments[$id] = $p;
            }
        }
    }

    public function getTransaction(string $apiKey, string $paymentId): ?array
    {
        return $this->payments[$paymentId] ?? null;
    }

    public function listTransactionsSince(string $apiKey, \DateTimeImmutable $since): array
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
