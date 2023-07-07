<?php

namespace App\Form\Member\Model;

use App\Client\Model\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateAccountData
{
    private string $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    public string $profileFirstName = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    public string $profileLastName = '';

    /**
     * @Assert\Choice({"membership.join.civility.mister", "membership.join.civility.miss"})
     */
    public ?string $profileFormalTitle = null;

    /**
     * @Assert\Length(max=50)
     */
    public ?string $profileMiddleName = null;

    /**
     * @Assert\LessThanOrEqual("15 years ago", message="")
     */
    public ?\DateTime $profileBirthdate = null;

    /**
     * @Assert\Choice({"", ""})
     */
    public ?string $profileGender = null;

    /**
     * @Assert\Length(max=50)
     */
    public ?string $profileNationality = null;

    /**
     * @Assert\Length(max=50)
     */
    public ?string $profileCompany = null;

    /**
     * @Assert\Length(max=50)
     */
    public ?string $profileJobTitle = null;

    public ?string $contactPhone = null;

    public ?string $contactWorkPhone = null;

    /**
     * @Assert\Length(max=100)
     */
    public ?string $socialFacebook = null;

    /**
     * @Assert\Length(max=100)
     */
    public ?string $socialTwitter = null;

    /**
     * @Assert\Length(max=100)
     */
    public ?string $socialLinkedIn = null;

    /**
     * @Assert\Length(max=100)
     */
    public ?string $socialTelegram = null;

    /**
     * @Assert\Length(max=100)
     */
    public ?string $socialWhatsapp = null;

    /**
     * @Assert\Length(max=100)
     */
    public ?string $addressStreetLine1 = null;

    /**
     * @Assert\Length(max=100)
     */
    public ?string $addressStreetLine2 = null;

    /**
     * @Assert\Length(min=2, max=10)
     */
    public ?string $addressZipCode = null;

    /**
     * @Assert\Length(max=100)
     */
    public ?string $addressCity = null;

    /**
     * @Assert\Country()
     */
    public ?string $addressCountry = null;

    public ?bool $settingsReceiveNewsletters = null;

    public ?bool $settingsReceiveSms = null;

    public ?bool $settingsReceiveCalls = null;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function createFromContact(ApiResource $contact): self
    {
        $self = new self($contact->email);
        $self->profileFirstName = $contact->profileFirstName;
        $self->profileLastName = $contact->profileLastName;
        $self->profileFormalTitle = $contact->profileFormalTitle;
        $self->profileMiddleName = $contact->profileMiddleName;
        $self->profileBirthdate = $contact->profileBirthdate ? new \DateTime($contact->profileBirthdate) : null;
        $self->profileGender = $contact->profileGender;
        $self->profileNationality = $contact->profileNationality;
        $self->profileCompany = $contact->profileCompany;
        $self->profileJobTitle = $contact->profileJobTitle;
        $self->contactPhone = $contact->contactPhone;
        $self->contactWorkPhone = $contact->contactWorkPhone;
        $self->socialFacebook = $contact->socialFacebook;
        $self->socialTwitter = $contact->socialTwitter;
        $self->socialLinkedIn = $contact->socialLinkedIn;
        $self->socialTelegram = $contact->socialTelegram;
        $self->socialWhatsapp = $contact->socialWhatsapp;
        $self->addressStreetLine1 = $contact->addressStreetLine1;
        $self->addressStreetLine2 = $contact->addressStreetLine2;
        $self->addressZipCode = $contact->addressZipCode;
        $self->addressCity = $contact->addressCity;
        $self->addressCountry = $contact->addressCountry;
        $self->settingsReceiveNewsletters = $contact->settingsReceiveNewsletters;
        $self->settingsReceiveSms = $contact->settingsReceiveSms;
        $self->settingsReceiveCalls = $contact->settingsReceiveCalls;

        return $self;
    }

    public function createApiPayload(): array
    {
        return [
            'email' => $this->email,
            'profileFirstName' => $this->profileFirstName,
            'profileLastName' => $this->profileLastName,
            'profileFormalTitle' => $this->profileFormalTitle,
            'profileMiddleName' => $this->profileMiddleName,
            'profileBirthdate' => $this->profileBirthdate?->format('Y-m-d'),
            'profileGender' => $this->profileGender,
            'profileNationality' => $this->profileNationality,
            'profileCompany' => $this->profileCompany,
            'profileJobTitle' => $this->profileJobTitle,
            'contactPhone' => $this->contactPhone,
            'contactWorkPhone' => $this->contactWorkPhone,
            'socialFacebook' => $this->socialFacebook,
            'socialTwitter' => $this->socialTwitter,
            'socialLinkedIn' => $this->socialLinkedIn,
            'socialTelegram' => $this->socialTelegram,
            'socialWhatsapp' => $this->socialWhatsapp,
            'addressStreetLine1' => $this->addressStreetLine1,
            'addressStreetLine2' => $this->addressStreetLine2,
            'addressZipCode' => $this->addressZipCode,
            'addressCity' => $this->addressCity,
            'addressCountry' => $this->addressCountry,
            'settingsReceiveNewsletters' => $this->settingsReceiveNewsletters,
            'settingsReceiveSms' => $this->settingsReceiveSms,
            'settingsReceiveCalls' => $this->settingsReceiveCalls,
        ];
    }
}
