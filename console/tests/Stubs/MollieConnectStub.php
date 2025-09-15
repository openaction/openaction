<?php

namespace App\Tests\Stubs;

use App\Bridge\Mollie\MollieConnect;

class MollieConnectStub extends MollieConnect
{
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
}

