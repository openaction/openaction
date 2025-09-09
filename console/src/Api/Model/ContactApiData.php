<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use App\Entity\Community\Contact;
use Symfony\Component\Validator\Constraints as Assert;

class ContactApiData
{
    #[Assert\Type(['string', 'null'])]
    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 250)]
    public $email;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 50)]
    public $profileFormalTitle;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $profileFirstName;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $profileMiddleName;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $profileLastName;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Date]
    public $profileBirthdate;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Choice(choices: Contact::GENDERS)]
    public $profileGender;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(min: 2, max: 2)]
    public $profileNationality;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $profileCompany;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $profileJobTitle;

    #[Assert\Type(['string', 'null'])]
    public $accountPassword;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 2)]
    public $accountLanguage;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 50)]
    public $contactPhone;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 50)]
    public $contactWorkPhone;

    #[Assert\All([new Assert\Email()])]
    #[Assert\Type(['array', 'null'])]
    public $contactAdditionalEmails;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $socialFacebook;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $socialTwitter;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $socialLinkedIn;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $socialTelegram;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $socialWhatsapp;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $socialInstagram;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $socialTikTok;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $socialBluesky;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 20)]
    public $addressStreetNumber;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $addressStreetLine1;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $addressStreetLine2;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $addressZipCode;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $addressCity;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 50)]
    public $addressCountry;

    #[Assert\Type(['bool', 'null'])]
    public $isDeceased;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 250)]
    public $recruitedBy;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $birthName;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 150)]
    public $birthCity;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(min: 2, max: 2)]
    public $birthCountryCode;

    #[Assert\Type(['bool', 'null'])]
    public $settingsReceiveNewsletters;

    #[Assert\Type(['bool', 'null'])]
    public $settingsReceiveSms;

    #[Assert\Type(['bool', 'null'])]
    public $settingsReceiveCalls;

    #[Assert\Type(['array', 'null'])]
    #[Assert\All(
        [
            new Assert\Collection(
                fields: [
                    'projectName' => new Assert\NotBlank(),
                    'projectId' => new Assert\NotBlank(),
                    'settingsReceiveNewsletters' => new Assert\Type(['bool', 'null']),
                    'settingsReceiveSms' => new Assert\Type(['bool', 'null']),
                    'settingsReceiveCalls' => new Assert\Type(['bool', 'null']),
                ],
            ),
        ]
    )]
    public $settingsByProject;

    #[Assert\Type(['array', 'null'])]
    public $metadataCustomFields;

    #[Assert\All([new Assert\Type('string')])]
    #[Assert\Type(['array', 'null'])]
    public $metadataTags;

    #[Assert\All([new Assert\Type('string')])]
    #[Assert\Type(['array', 'null'])]
    public $metadataTagsOverride;

    #[Assert\Type(['string', 'null'])]
    #[Assert\Length(max: 50)]
    public $metadataSource;

    #[Assert\Type(['string', 'null'])]
    public $metadataComment;

    #[Assert\Type(['array', 'null'])]
    #[Assert\All(
        [
            new Assert\Collection(
                fields: [
                    'label' => new Assert\NotBlank(),
                    'startAt' => new Assert\Optional([new Assert\Type('string')]),
                    'endAt' => new Assert\Optional([new Assert\Type('string')]),
                ],
                allowMissingFields: true,
            ),
        ]
    )]
    public $mandates;

    #[Assert\Type(['array', 'null'])]
    #[Assert\All(
        [
            new Assert\Collection(
                fields: [
                    'label' => new Assert\NotBlank(),
                    'startAt' => new Assert\Optional([new Assert\Type('string')]),
                ],
                allowMissingFields: true,
            ),
        ]
    )]
    public $commitments;

    public static function createFromPayload(array $data): self
    {
        $self = new self();
        foreach (get_object_vars($self) as $var => $value) {
            $self->{$var} = $data[$var] ?? null;
        }

        return $self;
    }
}
