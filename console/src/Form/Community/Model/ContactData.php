<?php

namespace App\Form\Community\Model;

use App\Entity\Area;
use App\Entity\Community\Contact;
use App\Entity\Organization;
use App\Util\Json;
use App\Validator\UniqueContactEmail;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[UniqueContactEmail(
    organizationField: 'inOrganization',
    contactField: 'updatingContact',
    emailField: 'email',
)]
class ContactData
{
    // Store organization ID, and optional contact ID being updated, to check for unicity
    public readonly Organization $inOrganization;
    public readonly ?Contact $updatingContact;

    #[Assert\Email]
    #[Assert\Length(max: 250)]
    public ?string $email = '';

    #[Assert\All([new Assert\Email()])]
    public ?array $additionalEmails = [];

    #[Assert\Image(maxSize: '5Mi', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $picture = null;

    #[Assert\Length(max: 20)]
    public ?string $profileFormalTitle = '';

    #[Assert\Length(max: 150)]
    public ?string $profileFirstName = '';

    #[Assert\Length(max: 150)]
    public ?string $profileMiddleName = '';

    #[Assert\Length(max: 150)]
    public ?string $profileLastName = '';

    #[Assert\Type(\DateTime::class)]
    public ?\DateTime $profileBirthdate = null;

    #[Assert\Choice(choices: Contact::GENDERS)]
    public ?string $profileGender = '';

    #[Assert\Country]
    public ?string $profileNationality = '';

    #[Assert\Length(max: 150)]
    public ?string $profileCompany = '';

    #[Assert\Length(max: 150)]
    public ?string $profileJobTitle = '';

    #[AssertPhoneNumber]
    public ?string $contactPhone = null;

    #[AssertPhoneNumber]
    public ?string $contactWorkPhone = null;

    #[Assert\All([new Assert\Email()])]
    public array $contactAdditionalEmails = [];

    #[Assert\Length(max: 150)]
    public ?string $socialFacebook = '';

    #[Assert\Length(max: 150)]
    public ?string $socialTwitter = '';

    #[Assert\Length(max: 150)]
    public ?string $socialLinkedIn = '';

    #[Assert\Length(max: 150)]
    public ?string $socialTelegram = '';

    #[Assert\Length(max: 150)]
    public ?string $socialWhatsapp = '';

    #[Assert\Length(max: 150)]
    public ?string $addressStreetLine1 = '';

    #[Assert\Length(max: 150)]
    public ?string $addressStreetLine2 = '';

    #[Assert\Length(max: 150)]
    public ?string $addressZipCode = '';

    #[Assert\Length(max: 150)]
    public ?string $addressCity = '';

    public ?Area $addressCountry = null;

    public bool $settingsReceiveNewsletters = true;
    public bool $settingsReceiveSms = true;
    public bool $settingsReceiveCalls = false;
    public array $settingsByProject = [];

    public ?string $metadataComment = '';
    public ?string $metadataTags = null;

    public function __construct(Organization $inOrganization, ?Contact $updatingContact = null)
    {
        $this->inOrganization = $inOrganization;
        $this->updatingContact = $updatingContact;
    }

    public static function createFromContact(Contact $contact): self
    {
        $self = new self($contact->getOrganization(), $contact);
        $self->email = $contact->getEmail();
        $self->additionalEmails = $contact->getContactAdditionalEmails();
        $self->profileFormalTitle = $contact->getProfileFormalTitle();
        $self->profileFirstName = $contact->getProfileFirstName();
        $self->profileMiddleName = $contact->getProfileMiddleName();
        $self->profileLastName = $contact->getProfileLastName();
        $self->profileBirthdate = $contact->getProfileBirthdate();
        $self->profileGender = $contact->getProfileGender();
        $self->profileNationality = $contact->getProfileNationality();
        $self->profileCompany = $contact->getProfileCompany();
        $self->profileJobTitle = $contact->getProfileJobTitle();
        $self->contactPhone = $contact->getContactPhone();
        $self->contactWorkPhone = $contact->getContactWorkPhone();
        $self->socialFacebook = $contact->getSocialFacebook();
        $self->socialTwitter = $contact->getSocialTwitter();
        $self->socialLinkedIn = $contact->getSocialLinkedIn();
        $self->socialTelegram = $contact->getSocialTelegram();
        $self->socialWhatsapp = $contact->getSocialWhatsapp();
        $self->addressStreetLine1 = $contact->getAddressStreetLine1();
        $self->addressStreetLine2 = $contact->getAddressStreetLine2();
        $self->addressZipCode = $contact->getAddressZipCode();
        $self->addressCity = $contact->getAddressCity();
        $self->addressCountry = $contact->getAddressCountry();
        $self->settingsReceiveNewsletters = $contact->hasSettingsReceiveNewsletters();
        $self->settingsReceiveSms = $contact->hasSettingsReceiveSms();
        $self->settingsReceiveCalls = $contact->hasSettingsReceiveCalls();
        $self->settingsByProject = $contact->getSettingsByProject();
        $self->metadataComment = $contact->getMetadataComment();

        $tags = [];
        foreach ($contact->getMetadataTags() as $tag) {
            $id = $tag->getId();
            $tags[] = ['id' => (string) $id, 'name' => $tag->getName(), 'slug' => $tag->getSlug()];
        }

        $self->metadataTags = $tags ? Json::encode($tags) : '';

        return $self;
    }

    #[Assert\Callback]
    public function validateMinimalData(ExecutionContextInterface $context)
    {
        if (!$this->email && !$this->profileLastName && !$this->contactPhone) {
            $context->buildViolation('console.organization.community.minimal_data_required')->addViolation();
        }
    }

    public function parseTags(): array
    {
        if (!$this->metadataTags) {
            return [];
        }

        try {
            return Json::decode($this->metadataTags);
        } catch (\Throwable) {
            return [];
        }
    }
}
