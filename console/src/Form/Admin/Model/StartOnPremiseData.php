<?php

namespace App\Form\Admin\Model;

use App\Form\Billing\Model\UpdateBillingDetailsData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class StartOnPremiseData
{
    #[Assert\NotBlank]
    public ?string $circonscription = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public ?string $politicalParty = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    public ?string $candidateName = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    public ?string $subdomain = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public ?string $adminEmail = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $billingName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $billingEmail = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $billingAddressStreetLine1 = null;

    #[Assert\Length(max: 200)]
    public ?string $billingAddressStreetLine2 = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 25)]
    public ?string $billingAddressPostalCode = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $billingAddressCity = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 2)]
    public ?string $billingAddressCountry = 'FR';

    public bool $enableWebsite = true;
    public bool $enableLocalPosts = true;
    public bool $enableDonation = true;
    public bool $enablePrint = true;

    #[Assert\Image(maxSize: '10M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $mainImage = null;

    public function createBillingDetailsUpdate(): UpdateBillingDetailsData
    {
        $data = new UpdateBillingDetailsData();
        $data->name = $this->billingName;
        $data->email = $this->billingEmail;
        $data->streetLine1 = $this->billingAddressStreetLine1;
        $data->streetLine2 = $this->billingAddressStreetLine2;
        $data->postalCode = $this->billingAddressPostalCode;
        $data->city = $this->billingAddressCity;
        $data->country = $this->billingAddressCountry;

        return $data;
    }
}
