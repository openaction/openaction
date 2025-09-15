<?php

namespace App\Bridge\Mollie;

class MockMollieConnect implements MollieConnectInterface
{
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
}
