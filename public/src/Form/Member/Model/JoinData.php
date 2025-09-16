<?php

namespace App\Form\Member\Model;

use App\Validator\MemberEmailNotAlreadyUsed;
use App\Validator\ValidArea;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ValidArea()
 */
class JoinData
{
    /**
     * @Assert\NotBlank()
     *
     * @Assert\Email()
     *
     * @Assert\Length(max=150)
     *
     * @MemberEmailNotAlreadyUsed(message="")
     */
    public string $email = '';

    /**
     * @Assert\NotBlank()
     *
     * @Assert\Length(min=8)
     */
    public string $password = '';

    /**
     * @Assert\NotBlank()
     *
     * @Assert\Length(max=50)
     */
    public string $profileFirstName = '';

    /**
     * @Assert\NotBlank()
     *
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

    public function createApiPayload(string $source): array
    {
        return [
            'email' => $this->email,
            'accountPassword' => $this->password,
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
            'metadataSource' => $source,
        ];
    }
}
