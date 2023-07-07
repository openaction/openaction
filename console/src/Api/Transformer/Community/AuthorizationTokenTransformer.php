<?php

namespace App\Api\Transformer\Community;

use App\Api\Transformer\AbstractTransformer;
use App\Community\Member\AuthorizationToken;

class AuthorizationTokenTransformer extends AbstractTransformer
{
    public function transform(AuthorizationToken $token)
    {
        return [
            '_resource' => 'AuthorizationToken',
            'firstName' => $token->getFirstName(),
            'lastName' => $token->getLastName(),
            'nonce' => $token->getNonce(),
            'encrypted' => $token->getEncrypted(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'AuthorizationToken';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'firstName' => 'string',
            'lastName' => 'string',
            'nonce' => 'string',
            'encrypted' => 'string',
        ];
    }
}
