<?php

namespace App\Entity\Community;

use App\Api\Model\ContactApiData;
use App\Entity\Area;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Entity\Website\FormAnswer;
use App\Form\Community\Model\ContactData;
use App\Repository\Community\ContactRepository;
use App\Search\Model\Searchable;
use App\Util\Address;
use App\Util\PhoneNumber;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber as PhoneNumberModel;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function Symfony\Component\String\u;

use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table('community_contacts')]
#[ORM\Index(name: 'community_contacts_email_idx', columns: ['email'])]
#[ORM\Index(name: 'community_contacts_email_organization_idx', columns: ['email', 'organization_id'])]
class Contact implements UserInterface, PasswordAuthenticatedUserInterface, Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    public const GENDERS = [
        'male',
        'female',
        'transgender',
        'non_binary',
        'other',
    ];

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $email;

    #[ORM\ManyToOne(targetEntity: Area::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Area $area;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $picture = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $profileFormalTitle = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileFirstName = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileFirstNameSlug = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileMiddleName = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileMiddleNameSlug = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileLastName = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileLastNameSlug = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $profileBirthdate = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $profileGender = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $profileNationality = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileCompany = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileCompanySlug = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileJobTitle = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $profileJobTitleSlug = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $accountPassword = null;

    #[ORM\Column(type: 'boolean')]
    private bool $accountConfirmed = false;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $accountConfirmToken;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $accountResetRequestedAt = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $accountResetToken;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $accountLanguage = null;

    #[ORM\Column(type: 'json')]
    private array $contactAdditionalEmails = [];

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $contactPhone = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $contactWorkPhone = null;

    #[ORM\Column(type: 'phone_number', nullable: true)]
    private ?PhoneNumberModel $parsedContactPhone = null;

    #[ORM\Column(type: 'phone_number', nullable: true)]
    private ?PhoneNumberModel $parsedContactWorkPhone = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialFacebook = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialTwitter = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialLinkedIn = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialTelegram = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialWhatsapp = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $addressStreetNumber = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $addressStreetLine1 = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $addressStreetLine1Slug = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $addressStreetLine2 = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $addressStreetLine2Slug = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $addressZipCode = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $addressCity = null;

    #[ORM\ManyToOne(targetEntity: Area::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Area $addressCountry = null;

    #[ORM\Column(type: 'boolean')]
    private bool $settingsReceiveNewsletters = true;

    #[ORM\Column(type: 'boolean')]
    private bool $settingsReceiveSms = true;

    #[ORM\Column(type: 'boolean')]
    private bool $settingsReceiveCalls = true;

    #[ORM\Column(type: 'jsonb')]
    private array $settingsByProject = [];

    #[ORM\Column(type: 'json')]
    private array $metadataCustomFields = [];

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'contacts', cascade: ['refresh'])]
    #[ORM\JoinTable(name: 'community_contacts_tags')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $metadataTags;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $metadataSource = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $metadataComment = null;

    #[ORM\OneToMany(targetEntity: EmailingCampaignMessage::class, mappedBy: 'contact', cascade: ['remove'])]
    private Collection $messages;

    #[ORM\OneToMany(targetEntity: FormAnswer::class, mappedBy: 'contact', cascade: ['remove'])]
    private Collection $formAnswers;

    #[ORM\OneToMany(targetEntity: ContactUpdate::class, mappedBy: 'contact', cascade: ['remove'])]
    private Collection $updates;

    #[ORM\OneToMany(targetEntity: ContactLog::class, mappedBy: 'contact', cascade: ['persist', 'remove'])]
    private Collection $logs;

    public function __construct(Organization $organization, ?string $email = null, Area $area = null)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->organization = $organization;
        $this->email = self::normalizeEmail($email);
        $this->area = $area;
        $this->metadataTags = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->formAnswers = new ArrayCollection();
        $this->updates = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    public function __toString(): string
    {
        return trim($this->profileFirstName.' '.$this->profileLastName) ?: $this->email ?: $this->contactPhone ?: '(-)';
    }

    public function __serialize(): array
    {
        return ['id' => $this->id, 'email' => self::normalizeEmail($this->email)];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->email = self::normalizeEmail($data['email']);
    }

    public static function normalizeEmail(?string $email): ?string
    {
        return u(trim($email ?: ''))->lower()->toString() ?: null;
    }

    /*
     * Factories
     */
    public static function createFixture(array $data): self
    {
        $self = new Contact($data['orga'], $data['email'], $data['area'] ?? null);
        $self->picture = $data['picture'] ?? null;
        $self->profileFormalTitle = $data['profileFormalTitle'] ?? null;
        $self->profileFirstName = $data['profileFirstName'] ?? null;
        $self->profileMiddleName = $data['profileMiddleName'] ?? null;
        $self->profileLastName = $data['profileLastName'] ?? null;
        $self->profileBirthdate = $data['profileBirthdate'] ?? null;
        $self->profileGender = $data['profileGender'] ?? null;
        $self->profileNationality = $data['profileNationality'] ?? null;
        $self->profileCompany = $data['profileCompany'] ?? null;
        $self->profileJobTitle = $data['profileJobTitle'] ?? null;
        $self->accountPassword = $data['accountPassword'] ?? null;
        $self->accountConfirmed = $data['accountConfirmed'] ?? !empty($data['accountPassword']);
        $self->accountConfirmToken = $data['accountConfirmToken'] ?? null;
        $self->accountResetToken = $data['accountResetToken'] ?? null;
        $self->accountResetRequestedAt = $data['accountResetRequestedAt'] ?? null;
        $self->accountLanguage = $data['accountLanguage'] ?? null;
        $self->socialFacebook = $data['socialFacebook'] ?? null;
        $self->socialTwitter = $data['socialTwitter'] ?? null;
        $self->socialLinkedIn = $data['socialLinkedIn'] ?? null;
        $self->socialTelegram = $data['socialTelegram'] ?? null;
        $self->socialWhatsapp = $data['socialWhatsapp'] ?? null;
        $self->addressStreetNumber = $data['addressStreetNumber'] ?? null;
        $self->addressStreetLine1 = $data['addressStreetLine1'] ?? null;
        $self->addressStreetLine2 = $data['addressStreetLine2'] ?? null;
        $self->addressZipCode = trim(str_replace(' ', '', (string) ($data['addressZipCode'] ?? null))) ?: null;
        $self->addressCity = Address::formatCityName($data['addressCity'] ?? null);
        $self->addressCountry = $data['addressCountry'] ?? null;
        $self->settingsReceiveNewsletters = $data['settingsReceiveNewsletters'] ?? true;
        $self->settingsReceiveSms = $data['settingsReceiveSms'] ?? true;
        $self->settingsReceiveCalls = $data['settingsReceiveCalls'] ?? true;
        $self->metadataCustomFields = $data['metadataCustomFields'] ?? [];
        $self->metadataComment = $data['metadataComment'] ?? null;
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->contactPhone = $data['contactPhone'] ?? null;
        $self->contactWorkPhone = $data['contactWorkPhone'] ?? null;
        $self->contactAdditionalEmails = $data['contactAdditionalEmails'] ?? [];

        // Slugs
        $slugger = new AsciiSlugger(strtolower($data['addressCountry'] ?? 'fr'));
        $self->profileFirstNameSlug = $slugger->slug((string) $self->profileFirstName, '-')->lower()->toString() ?: null;
        $self->profileMiddleNameSlug = $slugger->slug((string) $self->profileMiddleName, '-')->lower()->toString() ?: null;
        $self->profileLastNameSlug = $slugger->slug((string) $self->profileLastName, '-')->lower()->toString() ?: null;
        $self->profileCompanySlug = $slugger->slug((string) $self->profileCompany, '-')->lower()->toString() ?: null;
        $self->profileJobTitleSlug = $slugger->slug((string) $self->profileJobTitle, '-')->lower()->toString() ?: null;
        $self->addressStreetLine1Slug = $slugger->slug((string) $self->addressStreetLine1, '-')->lower()->toString() ?: null;
        $self->addressStreetLine2Slug = $slugger->slug((string) $self->addressStreetLine2, '-')->lower()->toString() ?: null;

        // Parse phone and work phone
        $self->refreshParsedPhoneNumbers();

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        /** @var Tag $tag */
        foreach ($data['tags'] ?? [] as $tag) {
            $self->getMetadataTags()->add($tag);
            $tag->getContacts()->add($self);
        }

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'contact';
    }

    public function getSearchOrganization(): string
    {
        return $this->organization->getUuid()->toRfc4122();
    }

    public function getSearchAccessibleFromProjects(): array
    {
        $uuids = [];
        foreach ($this->organization->getProjects() as $project) {
            if ($this->isInProject($project)) {
                $uuids[] = $project->getUuid()->toRfc4122();
            }
        }

        return $uuids;
    }

    public function isSearchPublic(): bool
    {
        return false;
    }

    public function getSearchUuid(): string
    {
        return $this->getUuid()->toRfc4122();
    }

    public function getSearchTitle(): string
    {
        return $this->getEmail();
    }

    public function getSearchContent(): ?string
    {
        return null;
    }

    public function getSearchCategoriesFacet(): array
    {
        return $this->getMetadataTagsNames();
    }

    public function getSearchStatusFacet(): string
    {
        return $this->isMember() ? 'member' : 'contact';
    }

    public function getSearchAreaTreeFacet(): array
    {
        return $this->getAreasTree();
    }

    public function getSearchDateFacet(): ?int
    {
        return (int) $this->createdAt->format('U');
    }

    public function getSearchMetadata(): array
    {
        return [
            'contactPhone' => $this->getContactPhone(),
            'contactWorkPhone' => $this->getContactWorkPhone(),
            'profileFormalTitle' => $this->getProfileFormalTitle(),
            'profileFirstName' => $this->getProfileFirstName(),
            'profileMiddleName' => $this->getProfileMiddleName(),
            'profileLastName' => $this->getProfileLastName(),
            'profileCompany' => $this->getProfileCompany(),
            'profileJobTitle' => $this->getProfileJobTitle(),
            'addressStreetNumber' => $this->getAddressStreetNumber(),
            'addressStreetLine1' => $this->getAddressStreetLine1(),
            'addressStreetLine2' => $this->getAddressStreetLine2(),
            'addressZipCode' => $this->getAddressZipCode(),
            'addressCity' => $this->getAddressCity(),
            'addressCountry' => $this->getAddressCountry() ? strtoupper($this->getAddressCountry()->getCode()) : null,
            'picture' => $this->picture?->getPathname(),
            'emailHash' => $this->getEmailHash(),
        ];
    }

    /*
     * Data updates
     */
    public function setCountry(?Area $country)
    {
        $this->addressCountry = $country;
    }

    public function setArea(?Area $area)
    {
        $this->area = $area;
    }

    public function changeEmail(?string $email)
    {
        $this->email = self::normalizeEmail($email);
    }

    public function changePassword(string $hashedPassword)
    {
        $this->accountPassword = $hashedPassword;
    }

    public function updateNewsletterSubscription(bool $subscribed, ?string $source = null)
    {
        if (!$this->settingsReceiveNewsletters && $subscribed) {
            $this->settingsReceiveNewsletters = true;
            $this->logs->add(new ContactLog($this, ContactLog::TYPE_NEWSLETTER_SUBSCRIBE, $source));
        } elseif ($this->settingsReceiveNewsletters && !$subscribed) {
            $this->settingsReceiveNewsletters = false;
            $this->logs->add(new ContactLog($this, ContactLog::TYPE_NEWSLETTER_UNSUBSCRIBE, $source));
        }
    }

    public function updateSmsSubscription(bool $subscribed, ?string $source = null)
    {
        if (!$this->settingsReceiveSms && $subscribed) {
            $this->settingsReceiveSms = true;
            $this->logs->add(new ContactLog($this, ContactLog::TYPE_SMS_SUBSCRIBE, $source));
        } elseif ($this->settingsReceiveSms && !$subscribed) {
            $this->settingsReceiveSms = false;
            $this->logs->add(new ContactLog($this, ContactLog::TYPE_SMS_UNSUBSCRIBE, $source));
        }
    }

    public function updateCallsSubscription(bool $subscribed, ?string $source = null)
    {
        if (!$this->settingsReceiveCalls && $subscribed) {
            $this->settingsReceiveCalls = true;
            $this->logs->add(new ContactLog($this, ContactLog::TYPE_CALLS_SUBSCRIBE, $source));
        } elseif ($this->settingsReceiveCalls && !$subscribed) {
            $this->settingsReceiveCalls = false;
            $this->logs->add(new ContactLog($this, ContactLog::TYPE_CALLS_UNSUBSCRIBE, $source));
        }
    }

    public function setPicture(?Upload $picture)
    {
        $this->picture = $picture;
    }

    public function setContactAdditionalEmails(array $emails)
    {
        $this->contactAdditionalEmails = array_filter(array_unique(array_values($emails)));
    }

    public function mergeMetadataComment(?string $comment)
    {
        $this->metadataComment .= "\n$comment";
    }

    public function mergeMetadataCustomFields(array $customFields)
    {
        $this->metadataCustomFields = array_merge($this->metadataCustomFields, $customFields);
    }

    public function applySettingsUpdate(bool $newsletter, bool $sms, bool $calls, ?string $source = null)
    {
        $this->updateNewsletterSubscription(subscribed: $newsletter, source: $source);
        $this->updateSmsSubscription(subscribed: $sms, source: $source);
        $this->updateCallsSubscription(subscribed: $calls, source: $source);
    }

    public function applyDataUpdate(ContactData $data, ?string $source = null)
    {
        $slugger = new AsciiSlugger(defaultLocale: strtolower($data->addressCountry ?: $this->addressCountry ?: 'fr'));

        $this->email = self::normalizeEmail($data->email);
        $this->contactAdditionalEmails = array_map([self::class, 'normalizeEmail'], array_filter(array_values($data->additionalEmails)));
        $this->profileFormalTitle = $data->profileFormalTitle;
        $this->profileFirstName = $data->profileFirstName;
        $this->profileFirstNameSlug = $slugger->slug((string) $data->profileFirstName, '-')->lower()->toString() ?: null;
        $this->profileMiddleName = $data->profileMiddleName;
        $this->profileMiddleNameSlug = $slugger->slug((string) $data->profileMiddleName, '-')->lower()->toString() ?: null;
        $this->profileLastName = $data->profileLastName;
        $this->profileLastNameSlug = $slugger->slug((string) $data->profileLastName, '-')->lower()->toString() ?: null;
        $this->profileBirthdate = $data->profileBirthdate;
        $this->profileGender = $data->profileGender;
        $this->profileNationality = $data->profileNationality;
        $this->profileCompany = $data->profileCompany;
        $this->profileCompanySlug = $slugger->slug((string) $data->profileCompany, '-')->lower()->toString() ?: null;
        $this->profileJobTitle = $data->profileJobTitle;
        $this->profileJobTitleSlug = $slugger->slug((string) $data->profileJobTitle, '-')->lower()->toString() ?: null;
        $this->socialFacebook = $data->socialFacebook;
        $this->socialTwitter = $data->socialTwitter;
        $this->socialLinkedIn = $data->socialLinkedIn;
        $this->socialTelegram = $data->socialTelegram;
        $this->socialWhatsapp = $data->socialWhatsapp;
        $this->addressStreetLine1 = $data->addressStreetLine1;
        $this->addressStreetLine1Slug = $slugger->slug((string) $data->addressStreetLine1, '-')->lower()->toString() ?: null;
        $this->addressStreetLine2 = $data->addressStreetLine2;
        $this->addressStreetLine2Slug = $slugger->slug((string) $data->addressStreetLine2, '-')->lower()->toString() ?: null;
        $this->addressZipCode = trim(str_replace(' ', '', (string) $data->addressZipCode)) ?: null;
        $this->addressCity = Address::formatCityName($data->addressCity);
        $this->addressCountry = $data->addressCountry;
        $this->metadataComment = $data->metadataComment;
        $this->contactPhone = $data->contactPhone;
        $this->contactWorkPhone = $data->contactWorkPhone;
        $this->updateNewsletterSubscription($data->settingsReceiveNewsletters, $source);
        $this->updateSmsSubscription($data->settingsReceiveSms, $source);
        $this->updateCallsSubscription($data->settingsReceiveCalls, $source);

        // Parse phone and work phone
        $this->refreshParsedPhoneNumbers();

        // Refresh settings
        $this->refreshSettings($data);
    }

    public function applyApiUpdate(ContactApiData $data, ?string $source = null)
    {
        // String fields
        $stringFields = [
            'profileFormalTitle',
            'profileFirstName',
            'profileMiddleName',
            'profileLastName',
            'profileGender',
            'profileNationality',
            'profileCompany',
            'profileJobTitle',
            'accountLanguage',
            'contactPhone',
            'contactWorkPhone',
            'socialFacebook',
            'socialTwitter',
            'socialLinkedIn',
            'socialTelegram',
            'socialWhatsapp',
            'addressStreetNumber',
            'addressStreetLine1',
            'addressStreetLine2',
            'metadataSource',
            'metadataComment',
        ];

        foreach ($stringFields as $field) {
            if ($value = trim((string) $data->{$field})) {
                $this->{$field} = $value;
            }
        }

        // GDPR fields
        if (null !== $data->settingsReceiveNewsletters) {
            $this->updateNewsletterSubscription($data->settingsReceiveNewsletters, $source);
        }

        if (null !== $data->settingsReceiveSms) {
            $this->updateSmsSubscription($data->settingsReceiveSms, $source);
        }

        if (null !== $data->settingsReceiveCalls) {
            $this->updateCallsSubscription($data->settingsReceiveCalls, $source);
        }

        // Array fields
        if ($data->contactAdditionalEmails) {
            $this->contactAdditionalEmails = array_merge($this->contactAdditionalEmails, array_values((array) $data->contactAdditionalEmails));
        }

        if ($data->metadataCustomFields) {
            $this->metadataCustomFields = array_merge($this->metadataCustomFields, (array) $data->metadataCustomFields);
        }

        $this->refreshSettings($data);

        // Format city
        if (trim((string) $data->addressZipCode)) {
            $this->addressZipCode = trim(str_replace(' ', '', $data->addressZipCode));
        }

        if (trim((string) $data->addressCity)) {
            $this->addressCity = Address::formatCityName($data->addressCity);
        }

        // Slugs
        $slugger = new AsciiSlugger(defaultLocale: strtolower($data->addressCountry ?: $this->addressCountry ?: 'fr'));

        if ($data->profileFirstName) {
            $this->profileFirstNameSlug = $slugger->slug($data->profileFirstName, '-')->lower()->toString();
        }

        if ($data->profileMiddleName) {
            $this->profileMiddleNameSlug = $slugger->slug($data->profileMiddleName, '-')->lower()->toString();
        }

        if ($data->profileLastName) {
            $this->profileLastNameSlug = $slugger->slug($data->profileLastName, '-')->lower()->toString();
        }

        if ($data->profileCompany) {
            $this->profileCompanySlug = $slugger->slug($data->profileCompany, '-')->lower()->toString();
        }

        if ($data->profileJobTitle) {
            $this->profileJobTitleSlug = $slugger->slug($data->profileJobTitle, '-')->lower()->toString();
        }

        if ($data->addressStreetLine1) {
            $this->addressStreetLine1Slug = $slugger->slug($data->addressStreetLine1, '-')->lower()->toString();
        }

        if ($data->addressStreetLine2) {
            $this->addressStreetLine2Slug = $slugger->slug($data->addressStreetLine2, '-')->lower()->toString();
        }

        // Parse birthdate
        if ($data->profileBirthdate) {
            try {
                $this->profileBirthdate = new \DateTime($data->profileBirthdate);
            } catch (\Exception) {
                // no-op
            }
        }

        // Parse phone and work phone
        $this->refreshParsedPhoneNumbers();
    }

    public function applyUnregister()
    {
        $this->area = null;
        $this->picture = null;
        $this->profileFormalTitle = null;
        $this->profileFirstName = null;
        $this->profileFirstNameSlug = null;
        $this->profileMiddleName = null;
        $this->profileMiddleNameSlug = null;
        $this->profileLastName = null;
        $this->profileLastNameSlug = null;
        $this->profileBirthdate = null;
        $this->profileGender = null;
        $this->profileNationality = null;
        $this->profileCompany = null;
        $this->profileCompanySlug = null;
        $this->profileJobTitle = null;
        $this->profileJobTitleSlug = null;
        $this->accountPassword = null;
        $this->accountConfirmed = false;
        $this->accountConfirmToken = null;
        $this->accountResetRequestedAt = null;
        $this->accountResetToken = null;
        $this->accountLanguage = null;
        $this->contactAdditionalEmails = [];
        $this->contactWorkPhone = null;
        $this->parsedContactWorkPhone = null;
        $this->socialFacebook = null;
        $this->socialTwitter = null;
        $this->socialLinkedIn = null;
        $this->socialTelegram = null;
        $this->socialWhatsapp = null;
        $this->addressStreetNumber = null;
        $this->addressStreetLine1 = null;
        $this->addressStreetLine1Slug = null;
        $this->addressStreetLine2 = null;
        $this->addressStreetLine2Slug = null;
        $this->addressZipCode = null;
        $this->addressCity = null;
        $this->settingsReceiveNewsletters = false;
        $this->settingsReceiveSms = false;
        $this->settingsReceiveCalls = false;
        $this->metadataCustomFields = [];
        $this->metadataSource = null;
        $this->metadataComment = null;
    }

    public function refreshParsedPhoneNumbers()
    {
        $defaultCountry = $this->getAddressCountry() ? strtoupper($this->getAddressCountry()->getCode()) : 'FR';

        // Try to parse contact phone and update it if it works
        if ($this->contactPhone) {
            $this->parsedContactPhone = PhoneNumber::parse($this->contactPhone, $defaultCountry);

            if ($this->parsedContactPhone) {
                $this->contactPhone = PhoneNumber::format($this->parsedContactPhone);
            }
        }

        // Try to parse contact phone and update it if it works
        if ($this->contactWorkPhone) {
            $this->parsedContactWorkPhone = PhoneNumber::parse($this->contactWorkPhone, $defaultCountry);

            if ($this->parsedContactWorkPhone) {
                $this->contactWorkPhone = PhoneNumber::format($this->parsedContactWorkPhone);
            }
        }
    }

    /*
     * Security
     */
    public function startAccountConfirmProcess(): bool
    {
        if ($this->accountConfirmed) {
            return false;
        }

        $this->accountConfirmToken = bin2hex(random_bytes(32));

        return true;
    }

    public function confirmAccount(string $token): bool
    {
        if (!$this->accountConfirmToken || $token !== $this->accountConfirmToken) {
            return false;
        }

        $this->accountConfirmed = true;
        $this->accountConfirmToken = null;

        return true;
    }

    public function startAccountResetProcess(): bool
    {
        // If there is an active reset request, ignore
        if ($this->accountResetRequestedAt && $this->accountResetRequestedAt > new \DateTime('24 hours ago')) {
            return false;
        }

        $this->accountResetToken = bin2hex(random_bytes(32));
        $this->accountResetRequestedAt = new \DateTime();

        return true;
    }

    public function resetAccount(string $token, string $password): bool
    {
        // If no request was done, ignore
        if (!$this->accountResetRequestedAt || $this->accountResetRequestedAt < new \DateTime('24 hours ago')) {
            return false;
        }

        // If the token is invalid, ignore
        if ($token !== $this->accountResetToken) {
            return false;
        }

        $this->accountPassword = $password;
        $this->accountResetToken = null;
        $this->accountResetRequestedAt = null;

        // If a reset worked, it means the email is valid
        $this->accountConfirmed = true;
        $this->accountConfirmToken = null;

        return true;
    }

    public function getRoles(): array
    {
        return []; // Ignored as contacts aren't authenticated on the Console
    }

    public function getPassword(): ?string
    {
        return $this->accountPassword;
    }

    public function getSalt(): ?string
    {
        return null; // No salt used
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function eraseCredentials()
    {
    }

    /*
     * Getters
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function isMember(): bool
    {
        return (bool) $this->accountPassword;
    }

    public function isInOrganization(Organization $organization): bool
    {
        return $organization->getId() === $this->organization->getId();
    }

    public function isInProject(Project $project): bool
    {
        if (!$this->isInOrganization($project->getOrganization())) {
            return false;
        }

        if ($project->isGlobal()) {
            return true;
        }

        if ($project->isThematic()) {
            if (!$this->metadataTags->count()) {
                return false;
            }

            foreach ($project->getTags() as $tag) {
                if ($this->hasMetadataTag($tag)) {
                    return true;
                }
            }
        }

        if ($project->isLocal()) {
            if (!$this->area) {
                return false;
            }

            foreach ($project->getAreas() as $area) {
                if ($area->contains($this->area)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getFullName()
    {
        return $this->profileFirstName.' '.$this->profileLastName;
    }

    public function getCompleteFullName()
    {
        return implode(' ', array_filter([
            $this->profileFormalTitle,
            $this->profileFirstName,
            $this->profileMiddleName,
            $this->profileLastName,
        ]));
    }

    public function getJobReference()
    {
        return implode(', ', array_filter([$this->profileJobTitle, $this->profileCompany]));
    }

    public function getPostalAddress()
    {
        return implode("\n", array_filter([
            trim($this->addressStreetNumber.' '.$this->addressStreetLine1),
            $this->addressStreetLine2,
            trim($this->addressZipCode.' '.$this->addressCity),
            $this->addressCountry ? $this->addressCountry->getName() : '',
        ]));
    }

    public function getEmailHash(): ?string
    {
        return $this->email ? md5($this->email) : null;
    }

    public function getMetadataTagsIds(): array
    {
        $ids = $this->metadataTags->map(fn (Tag $t) => $t->getId())->toArray();
        sort($ids);

        return array_values($ids);
    }

    public function getMetadataTagsNames(): array
    {
        $names = $this->metadataTags->map(fn (Tag $t) => $t->getName())->toArray();
        sort($names);

        return array_values($names);
    }

    public function getMetadataTagsList(int $maxLength = 50): string
    {
        $names = u(implode(', ', $this->metadataTags->map(fn (Tag $t) => $t->getName())->toArray()));

        if ($names->length() <= $maxLength) {
            return $names;
        }

        return $names->truncate($maxLength - 3).'...';
    }

    public function hasMetadataTag(Tag $tag)
    {
        foreach ($this->metadataTags as $metadataTag) {
            if ($tag->getId() === $metadataTag->getId()) {
                return true;
            }
        }

        return false;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPicture(): ?Upload
    {
        return $this->picture;
    }

    /**
     * Resolve all the areas in which this contact is located.
     */
    public function getAreasTree(): array
    {
        $tree = [];

        $area = $this->area;
        while ($area) {
            $tree[] = $area->getId();
            $area = $area->getParent();
        }

        return $tree;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function getProfileFormalTitle(): ?string
    {
        return $this->profileFormalTitle;
    }

    public function getProfileFirstName(): ?string
    {
        return $this->profileFirstName;
    }

    public function getProfileMiddleName(): ?string
    {
        return $this->profileMiddleName;
    }

    public function getProfileLastName(): ?string
    {
        return $this->profileLastName;
    }

    public function getProfileBirthdate(): ?\DateTime
    {
        return $this->profileBirthdate;
    }

    public function getProfileGender(): ?string
    {
        return $this->profileGender;
    }

    public function getProfileNationality(): ?string
    {
        return $this->profileNationality;
    }

    public function getProfileCompany(): ?string
    {
        return $this->profileCompany;
    }

    public function getProfileJobTitle(): ?string
    {
        return $this->profileJobTitle;
    }

    public function getAccountPassword(): ?string
    {
        return $this->accountPassword;
    }

    public function isAccountConfirmed(): bool
    {
        return $this->accountConfirmed;
    }

    public function getAccountConfirmToken(): ?string
    {
        return $this->accountConfirmToken;
    }

    public function getAccountResetToken(): ?string
    {
        return $this->accountResetToken;
    }

    public function getAccountResetRequestedAt(): ?\DateTime
    {
        return $this->accountResetRequestedAt;
    }

    public function getAccountLanguage(): ?string
    {
        return $this->accountLanguage;
    }

    public function getContactAdditionalEmails(): array
    {
        return $this->contactAdditionalEmails;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function getContactWorkPhone(): ?string
    {
        return $this->contactWorkPhone;
    }

    public function getParsedContactPhone(): ?PhoneNumberModel
    {
        return $this->parsedContactPhone;
    }

    public function getParsedContactWorkPhone(): ?PhoneNumberModel
    {
        return $this->parsedContactWorkPhone;
    }

    public function getSocialFacebook(): ?string
    {
        return $this->socialFacebook;
    }

    public function getSocialTwitter(): ?string
    {
        return $this->socialTwitter;
    }

    public function getSocialLinkedIn(): ?string
    {
        return $this->socialLinkedIn;
    }

    public function getSocialTelegram(): ?string
    {
        return $this->socialTelegram;
    }

    public function getSocialWhatsapp(): ?string
    {
        return $this->socialWhatsapp;
    }

    public function getAddressStreetNumber(): ?string
    {
        return $this->addressStreetNumber;
    }

    public function getAddressStreetLine1(): ?string
    {
        return $this->addressStreetLine1;
    }

    public function getAddressStreetLine2(): ?string
    {
        return $this->addressStreetLine2;
    }

    public function getAddressZipCode(): ?string
    {
        return $this->addressZipCode;
    }

    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    public function getAddressCountry(): ?Area
    {
        return $this->addressCountry;
    }

    public function hasSettingsReceiveNewsletters(): bool
    {
        return $this->settingsReceiveNewsletters;
    }

    public function hasSettingsReceiveSms(): bool
    {
        return $this->settingsReceiveSms;
    }

    public function hasSettingsReceiveCalls(): bool
    {
        return $this->settingsReceiveCalls;
    }

    public function getMetadataCustomFields(): array
    {
        return $this->metadataCustomFields;
    }

    public function getMetadataSource(): ?string
    {
        return $this->metadataSource;
    }

    public function getMetadataComment(): ?string
    {
        return $this->metadataComment;
    }

    public function getProfileFirstNameSlug(): ?string
    {
        return $this->profileFirstNameSlug;
    }

    public function getProfileMiddleNameSlug(): ?string
    {
        return $this->profileMiddleNameSlug;
    }

    public function getProfileLastNameSlug(): ?string
    {
        return $this->profileLastNameSlug;
    }

    public function getProfileCompanySlug(): ?string
    {
        return $this->profileCompanySlug;
    }

    public function getProfileJobTitleSlug(): ?string
    {
        return $this->profileJobTitleSlug;
    }

    public function getAddressStreetLine1Slug(): ?string
    {
        return $this->addressStreetLine1Slug;
    }

    public function getAddressStreetLine2Slug(): ?string
    {
        return $this->addressStreetLine2Slug;
    }

    /**
     * @return Tag[]|Collection
     */
    public function getMetadataTags(): Collection
    {
        return $this->metadataTags;
    }

    /**
     * @return Collection|EmailingCampaignMessage[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * @return Collection|FormAnswer[]
     */
    public function getFormAnswers(): Collection
    {
        return $this->formAnswers;
    }

    /**
     * @return Collection|ContactUpdate[]
     */
    public function getUpdates(): Collection
    {
        return $this->updates;
    }

    /**
     * @return Collection|ContactLog[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function getSettingsByProject(): array
    {
        return $this->settingsByProject;
    }

    public function setSettingsByProject(array $settingsByProject): void
    {
        $this->settingsByProject = $settingsByProject;
    }

    private function refreshSettings(ContactApiData|ContactData $data): void
    {
        if ($data->settingsByProject) {
            foreach ($data->settingsByProject as $projectId => $s) {
                if (true === $data->settingsReceiveNewsletters) {
                    $data->settingsByProject[$projectId]['settingsReceiveNewsletters'] = true;
                }

                if (true === $data->settingsReceiveSms) {
                    $data->settingsByProject[$projectId]['settingsReceiveSms'] = true;
                }

                if (true === $data->settingsReceiveCalls) {
                    $data->settingsByProject[$projectId]['settingsReceiveCalls'] = true;
                }
            }

            $this->settingsByProject = $data->settingsByProject;
        }
    }
}
