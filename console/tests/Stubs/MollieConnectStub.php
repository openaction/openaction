<?php

namespace App\Tests\Stubs;

class MollieConnectStub
{
    public function getAuthorizationUrl(string $state, array $scopes): string
    {
        return 'https://example.test/oauth?state='.$state;
    }

    /**
     * @return array{access_token:string, refresh_token:string, expires_in?:int}
     */
    public function exchangeCodeForTokens(string $code): array
    {
        return [
            'access_token' => 'stub_access',
            'refresh_token' => 'stub_refresh',
            'expires_in' => 3600,
        ];
    }

    /**
     * @return array{access_token:string, refresh_token:string, expires_in?:int}
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        return [
            'access_token' => 'refreshed_access',
            'refresh_token' => 'refreshed_refresh',
            'expires_in' => 3600,
        ];
    }
}

