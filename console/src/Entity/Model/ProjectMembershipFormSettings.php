<?php

namespace App\Entity\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ProjectMembershipFormSettings
{
    public const ASK_ON_REGISTRATION_AS_REQUIRED = 'membership.form.rule.required';
    public const ASK_ON_REGISTRATION_AS_OPTIONAL = 'membership.form.rule.optional';
    public const ASK_ON_ACCOUNT_UPDATE = 'membership.form.rule.update';
    public const DO_NOT_ASK = 'membership.form.rule.ignore';

    public ?string $introduction;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $profileFormalTitle;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $profileMiddleName;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $profileBirthdate;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $profileGender;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $profileNationality;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $profileCompany;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $profileJobTitle;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $contactPhone;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $contactWorkPhone;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $socialFacebook;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $socialTwitter;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $socialLinkedIn;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $socialTelegram;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $socialWhatsapp;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $addressStreetLine1;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $addressStreetLine2;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $addressZipCode;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $addressCity;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $addressCountry;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $settingsReceiveNewsletters;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $settingsReceiveSms;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getAvailableChoices')]
    public ?string $settingsReceiveCalls;

    public function __construct(array $data)
    {
        $this->introduction = $data['introduction'] ?? '';
        $this->profileFormalTitle = $data['profileFormalTitle'] ?? self::DO_NOT_ASK;
        $this->profileMiddleName = $data['profileMiddleName'] ?? self::DO_NOT_ASK;
        $this->profileBirthdate = $data['profileBirthdate'] ?? self::ASK_ON_REGISTRATION_AS_REQUIRED;
        $this->profileGender = $data['profileGender'] ?? self::DO_NOT_ASK;
        $this->profileNationality = $data['profileNationality'] ?? self::DO_NOT_ASK;
        $this->profileCompany = $data['profileCompany'] ?? self::DO_NOT_ASK;
        $this->profileJobTitle = $data['profileJobTitle'] ?? self::DO_NOT_ASK;
        $this->contactPhone = $data['contactPhone'] ?? self::ASK_ON_REGISTRATION_AS_OPTIONAL;
        $this->contactWorkPhone = $data['contactWorkPhone'] ?? self::ASK_ON_REGISTRATION_AS_OPTIONAL;
        $this->socialFacebook = $data['socialFacebook'] ?? self::DO_NOT_ASK;
        $this->socialTwitter = $data['socialTwitter'] ?? self::DO_NOT_ASK;
        $this->socialLinkedIn = $data['socialLinkedIn'] ?? self::DO_NOT_ASK;
        $this->socialTelegram = $data['socialTelegram'] ?? self::DO_NOT_ASK;
        $this->socialWhatsapp = $data['socialWhatsapp'] ?? self::DO_NOT_ASK;
        $this->addressStreetLine1 = $data['addressStreetLine1'] ?? self::ASK_ON_REGISTRATION_AS_REQUIRED;
        $this->addressStreetLine2 = $data['addressStreetLine2'] ?? self::ASK_ON_REGISTRATION_AS_OPTIONAL;
        $this->addressZipCode = $data['addressZipCode'] ?? self::ASK_ON_REGISTRATION_AS_REQUIRED;
        $this->addressCity = $data['addressCity'] ?? self::ASK_ON_REGISTRATION_AS_REQUIRED;
        $this->addressCountry = $data['addressCountry'] ?? self::ASK_ON_REGISTRATION_AS_REQUIRED;
        $this->settingsReceiveNewsletters = $data['settingsReceiveNewsletters'] ?? self::ASK_ON_REGISTRATION_AS_OPTIONAL;
        $this->settingsReceiveSms = $data['settingsReceiveSms'] ?? self::ASK_ON_REGISTRATION_AS_OPTIONAL;
        $this->settingsReceiveCalls = $data['settingsReceiveCalls'] ?? self::DO_NOT_ASK;
    }

    public function toArray(): array
    {
        return [
            'introduction' => $this->introduction,
            'profileFormalTitle' => $this->profileFormalTitle,
            'profileMiddleName' => $this->profileMiddleName,
            'profileBirthdate' => $this->profileBirthdate,
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

    public static function getAvailableChoices(): array
    {
        return [
            self::ASK_ON_REGISTRATION_AS_REQUIRED,
            self::ASK_ON_REGISTRATION_AS_OPTIONAL,
            self::ASK_ON_ACCOUNT_UPDATE,
            self::DO_NOT_ASK,
        ];
    }
}
