<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class ContactUpdateToken extends Constraint
{
    public const INVALID_TOKEN = '4e626659-44c9-43d1-905e-62a0f346bd64';
    public const TOKEN_EXPIRE = '8fe3cead-3ae1-4311-8cdd-ca76e8609d8d';
    public const INVALID_INSTANCE = 'f96fb6ac-3cea-4115-ace9-15187d983868';

    protected static $errorNames = [
        self::INVALID_INSTANCE => 'INVALID_INSTANCE',
        self::INVALID_TOKEN => 'INVALID_TOKEN',
        self::TOKEN_EXPIRE => 'TOKEN_EXPIRE',
    ];

    public string $message = 'Token invalid.';
    public ?string  $token;
    public ?int $daysToExpire = 2;

    public function __construct(
        array $options = null,
        string $message = null,
        string $token = null,
        int $daysToExpire = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->token = $token;
        $this->daysToExpire = $daysToExpire ?? $this->daysToExpire;
    }
}
