<?php

namespace App\Bridge\Mollie;

interface MollieConnectInterface
{
    public function getAuthorizationUrl(string $state, array $scopes): string;

    /**
     * @return array{access_token:string, refresh_token:string, expires_in?:int, token_type?:string, scope?:string}
     */
    public function exchangeCodeForTokens(string $code): array;
}

