<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidArea extends Constraint
{
    public const INVALID_CONTACT_STATUS = 'invalid_city';

    protected static $errorNames = [
        self::INVALID_CONTACT_STATUS => 'INVALID_CONTACT_STATUS',
    ];

    // By default, check only EU countries ZIP codes
    public $checkCountries = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HR', 'HU',
        'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];

    public $countryField = 'addressCountry';
    public $zipCodeField = 'addressZipCode';
    public $message = 'Ce code postal semble invalide pour le pays choisi.';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
