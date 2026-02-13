<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ContactPaymentScheduleApiData
{
    #[Assert\Type(['string', 'null'])]
    public $contactId;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 250)]
    public $email;

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
    #[Assert\Choice(['Card', 'Wire', 'Check', 'Cash', 'Sepa', 'Other'])]
    public $paymentMethod;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    #[Assert\Positive]
    public $intervalInMonths;

    #[Assert\NotBlank]
    #[Assert\Date]
    public $startDate;

    #[Assert\Type(['int', 'null'])]
    #[Assert\Positive]
    public $occurrences;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Date]
    public $endDate;

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

    #[Assert\Type(['array', 'null'])]
    public $metadata;

    #[Assert\Callback]
    public function validateSchedule(ExecutionContextInterface $context): void
    {
        if (!$this->contactId && !$this->email) {
            $context->buildViolation('You must provide either contactId or email')
                ->atPath('contactId')
                ->addViolation();
        }

        if (null === $this->occurrences && null === $this->endDate) {
            $context->buildViolation('You must provide either occurrences or endDate')
                ->atPath('occurrences')
                ->addViolation();
        }
    }

    public static function createFromPayload(array $data): self
    {
        $self = new self();
        foreach (get_object_vars($self) as $var => $value) {
            $self->{$var} = $data[$var] ?? null;
        }

        return $self;
    }
}
