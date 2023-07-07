<?php

namespace App\Form\Community\Printing\Model;

use App\Entity\Billing\Model\OrderRecipient;
use App\Entity\Organization;
use App\Entity\User;
use App\Form\Billing\Model\UpdateBillingDetailsData;
use Symfony\Component\Validator\Constraints as Assert;

class PrintingOrderBuyData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $billingOrganization = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 100)]
    public ?string $billingEmail = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $billingStreetLine1 = null;

    #[Assert\Length(max: 200)]
    public ?string $billingStreetLine2 = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 25)]
    public ?string $billingPostalCode = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $billingCity = null;

    #[Assert\NotBlank]
    #[Assert\Country]
    public ?string $billingCountry = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $recipientFirstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $recipientLastName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $recipientEmail = null;

    public function __construct(Organization $orga, User $user)
    {
        $this->billingOrganization = $orga->getBillingName();
        $this->billingEmail = $orga->getBillingEmail();
        $this->billingStreetLine1 = $orga->getBillingAddressStreetLine1();
        $this->billingStreetLine2 = $orga->getBillingAddressStreetLine2();
        $this->billingPostalCode = $orga->getBillingAddressPostalCode();
        $this->billingCity = $orga->getBillingAddressCity();
        $this->billingCountry = $orga->getBillingAddressCountry();
        $this->recipientFirstName = $user->getFirstName();
        $this->recipientLastName = $user->getLastName();
        $this->recipientEmail = $user->getEmail();
    }

    public function createUpdateBillingDetailsData(): UpdateBillingDetailsData
    {
        $data = new UpdateBillingDetailsData();
        $data->name = $this->billingOrganization;
        $data->email = $this->billingEmail;
        $data->streetLine1 = $this->billingStreetLine1;
        $data->streetLine2 = $this->billingStreetLine2;
        $data->postalCode = $this->billingPostalCode;
        $data->city = $this->billingCity;
        $data->country = $this->billingCountry;

        return $data;
    }

    public function createOrderRecipient(string $locale): OrderRecipient
    {
        return new OrderRecipient($this->recipientFirstName, $this->recipientLastName, $this->recipientEmail, $locale);
    }
}
