<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class MemberEmailNotAlreadyUsed extends Constraint
{
    public const INVALID_CONTACT_STATUS = 'invalid_contact_status';

    protected static $errorNames = [
        self::INVALID_CONTACT_STATUS => 'INVALID_CONTACT_STATUS',
    ];

    public $message = 'Cet email existe déjà.';

    public function __construct(
        ?array $options = null,
        ?array $groups = null,
        $payload = null,
        ?string $message = null,
    ) {
        parent::__construct($options, $groups, $payload);

        if (null !== $message) {
            $this->message = $message;
        }
    }

    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
