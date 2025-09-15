<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ContactPaymentApiData
{
    // Identify contact by ID (base62) or email
    #[Assert\Type(['string', 'null'])]
    public $contactId;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 250)]
    public $email;

    // Payment core
    #[Assert\NotBlank]
    #[Assert\Choice(['Donation', 'Membership', 'ElectedOfficialContribution'])]
    public $type;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    public $netAmount;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    public $feesAmount;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 3)]
    public $currency;

    #[Assert\NotBlank]
    #[Assert\Choice(['Mollie', 'Manual'])]
    public $paymentProvider;

    #[Assert\NotBlank]
    #[Assert\Choice(['Card', 'Wire', 'Check', 'Cash', 'Sepa', 'Other'])]
    public $paymentMethod;

    // Provider-specific details; for Mollie, expects { transactionId: string, rawPayload: array }
    #[Assert\Type(['array', 'null'])]
    public $paymentProviderDetails;

    // Payer snapshot (optional)
    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 50)]
    public $civility;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $firstName;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $lastName;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 250)]
    public $payerEmail;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $streetAddressLine1;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $streetAddressLine2;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $city;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 20)]
    public $postalCode;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(min: 2, max: 2)]
    public $countryCode;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Date]
    public $birthdate;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 50)]
    public $phone;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(min: 2, max: 2)]
    public $nationality;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(min: 2, max: 2)]
    public $fiscalCountryCode;

    // Arbitrary metadata (optional)
    #[Assert\Type(['array', 'null'])]
    public $metadata;

    public static function createFromPayload(array $data): self
    {
        $self = new self();
        foreach (get_object_vars($self) as $var => $value) {
            $self->{$var} = $data[$var] ?? null;
        }

        return $self;
    }
}
