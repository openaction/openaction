<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UniqueContactEmail extends Constraint
{
    public const EMAIL_ALREADY_EXISTS = '7b7c5047-c924-4be4-beb5-213885a3b552';

    protected static $errorNames = [
        self::EMAIL_ALREADY_EXISTS => 'EMAIL_ALREADY_EXISTS',
    ];

    public function __construct(
        public readonly string $organizationField,
        public readonly string $contactField,
        public readonly string $emailField,
        array $options = null,
        array $groups = null,
        $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
