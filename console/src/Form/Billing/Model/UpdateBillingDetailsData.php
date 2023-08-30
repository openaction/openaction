<?php

namespace App\Form\Billing\Model;

use App\Entity\Organization;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateBillingDetailsData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 100)]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $streetLine1 = null;

    #[Assert\Length(max: 200)]
    public ?string $streetLine2 = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 25)]
    public ?string $postalCode = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $city = null;

    #[Assert\NotBlank]
    #[Assert\Country]
    public ?string $country = null;

    public static function createFromOrganization(Organization $organization): self
    {
        $self = new self();
        $self->name = $organization->getBillingName();
        $self->email = $organization->getBillingEmail();
        $self->streetLine1 = $organization->getBillingAddressStreetLine1();
        $self->streetLine2 = $organization->getBillingAddressStreetLine2();
        $self->postalCode = $organization->getBillingAddressPostalCode();
        $self->city = $organization->getBillingAddressCity();
        $self->country = $organization->getBillingAddressCountry();

        return $self;
    }

    public static function createFromArray(array $details): self
    {
        $self = new self();
        $self->name = $details['name'];
        $self->email = $details['email'];
        $self->streetLine1 = $details['streetLine1'];
        $self->streetLine2 = $details['streetLine2'] ?? null;
        $self->postalCode = $details['postalCode'];
        $self->city = $details['city'];
        $self->country = $details['country'];

        return $self;
    }
}
