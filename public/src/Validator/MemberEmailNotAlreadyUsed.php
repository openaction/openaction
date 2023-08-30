<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MemberEmailNotAlreadyUsed extends Constraint
{
    public const INVALID_CONTACT_STATUS = 'invalid_contact_status';

    protected static $errorNames = [
        self::INVALID_CONTACT_STATUS => 'INVALID_CONTACT_STATUS',
    ];

    public $message = 'Cet email existe déjà.';

    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
