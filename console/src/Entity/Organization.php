<?php

namespace App\Entity;

use App\Entity\Community\Contact;
use App\Entity\Community\Tag;
use App\Entity\Model\SubscriptionNotifications;
use App\Entity\Theme\WebsiteTheme;
use App\Form\Billing\Model\UpdateBillingDetailsData;
use App\Platform\Plans;
use App\Repository\OrganizationRepository;
use App\Search\CrmIndexer;
use App\Util\Address;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\Table('organizations')]
class Organization
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    private bool $isDemo = false;

    #[ORM\Column(type: 'boolean')]
    private bool $showPreview = false;

    #[ORM\Column(length: 64, unique: true)]
    private string $apiToken;

    /**
     * The CRM index version to use for this organization (multiple versions can be available for atomic upgrades).
     */
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $crmIndexVersion = null;

    /**
     * The admin search key for this organization. This key can access all documents in this organization CRM.
     * For user-specific access keys, {@see OrganizationMember::$crmTenantToken}.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $crmSearchKey = null;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?Uuid $crmSearchKeyUid = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $partner = null;

    #[ORM\Column(length: 30)]
    private string $subscriptionPlan = Plans::PREMIUM;

    #[ORM\Column(type: 'boolean')]
    private bool $subscriptionTrialing = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $subscriptionCurrentPeriodEnd = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $subscriptionNotifications = [];

    #[ORM\Column(type: 'integer')]
    private int $projectsSlots = 1;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $creditsBalance = 0;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $textsCreditsBalance = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $billingPricePerMonth = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $mollieCustomerId = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $billingName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $billingEmail = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $billingAddressStreetLine1 = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $billingAddressStreetLine2 = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $billingAddressPostalCode = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $billingAddressCity = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $billingAddressCountry = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $billingTaxId = null;

    #[ORM\OneToMany(targetEntity: OrganizationMainTag::class, mappedBy: 'organization')]
    #[ORM\OrderBy(['weight' => 'ASC'])]
    private Collection $mainTags;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $forceTwoFactorAuth = false;

    /**
     * Whether the contacts main tags in the Community are used for progress
     * (ie when clicking on one, the previous ones are enabled automatically).
     */
    #[ORM\Column(type: 'boolean')]
    private bool $mainTagsIsProgress = false;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $quorumToken = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $quorumDefaultCity = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $spallianEndpoint = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $consoleCustomCss = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $textingSenderCode = null;

    /*
     * Emails provider
     */

    #[ORM\Column(type: 'boolean')]
    private bool $emailEnableOpenTracking = true;

    #[ORM\Column(type: 'boolean')]
    private bool $emailEnableClickTracking = true;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $emailThrottlingPerHour = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $emailProvider = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mailchimpServerPrefix = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mailchimpApiKey = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mailchimpAudienceName = null;

    /*
     * White label
     */

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $whiteLabelLogo = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $whiteLabelName = null;

    /*
     * Relationships
     */

    /**
     * @var Collection|Domain[]
     */
    #[ORM\OneToMany(targetEntity: Domain::class, mappedBy: 'organization', orphanRemoval: true)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $domains;

    /**
     * @var Collection|Project[]
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'organization', orphanRemoval: true, fetch: 'EXTRA_LAZY')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $projects;

    /**
     * @var Collection|OrganizationMember[]
     */
    #[ORM\OneToMany(targetEntity: OrganizationMember::class, mappedBy: 'organization', orphanRemoval: true, fetch: 'EXTRA_LAZY')]
    private Collection $members;

    /**
     * @var Collection|Registration[]
     */
    #[ORM\OneToMany(targetEntity: Registration::class, mappedBy: 'organization')]
    private Collection $invites;

    /**
     * @var Collection|SubscriptionLog[]
     */
    #[ORM\OneToMany(targetEntity: SubscriptionLog::class, mappedBy: 'organization', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $subscriptionLogs;

    /**
     * @var Collection|Contact[]
     */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'organization', orphanRemoval: true, fetch: 'EXTRA_LAZY')]
    private Collection $contacts;

    /**
     * @var Collection|WebsiteTheme[]
     */
    #[ORM\ManyToMany(targetEntity: WebsiteTheme::class, mappedBy: 'forOrganizations', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'organizations_website_themes')]
    private Collection $websiteThemes;

    public function __construct(string $name)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->name = $name;
        $this->apiToken = bin2hex(random_bytes(32));
        $this->mainTags = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->invites = new ArrayCollection();
        $this->subscriptionLogs = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->websiteThemes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /*
     * Factories
     */
    public static function createTrialing(string $name, string $plan, self $duplicate = null): self
    {
        $self = new self($name);
        $self->subscriptionTrialing = true;
        $self->subscriptionCurrentPeriodEnd = new \DateTime('+3 months');
        $self->subscriptionPlan = $plan;
        $self->addCredits(50);

        if ($duplicate) {
            $self->showPreview = $duplicate->showPreview;
            $self->partner = $duplicate->partner;
            $self->consoleCustomCss = $duplicate->consoleCustomCss;
            $self->textingSenderCode = $duplicate->textingSenderCode;
        }

        return $self;
    }

    public static function createOnPremise(string $name, string $plan): self
    {
        $self = new self($name);
        $self->subscriptionTrialing = false;
        $self->subscriptionCurrentPeriodEnd = new \DateTime('+30 years');
        $self->subscriptionPlan = $plan;
        $self->projectsSlots = 2;
        $self->addCredits(36000);

        return $self;
    }

    public static function createDemo(string $name): self
    {
        $self = new self($name);
        $self->isDemo = true;
        $self->subscriptionTrialing = false;
        $self->subscriptionCurrentPeriodEnd = new \DateTime('+20 years');
        $self->addCredits(100);

        return $self;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['name'] ?? 'Example orga');
        $self->partner = $data['partner'] ?? null;
        $self->showPreview = $data['showPreview'] ?? true;
        $self->quorumToken = $data['quorumToken'] ?? null;
        $self->quorumDefaultCity = $data['quorumDefaultCity'] ?? null;
        $self->apiToken = $data['apiToken'] ?? $self->apiToken;
        $self->spallianEndpoint = $data['spallianEndpoint'] ?? null;
        $self->subscriptionPlan = $data['subscriptionPlan'] ?? Plans::ORGANIZATION;
        $self->mollieCustomerId = $data['mollieCustomerId'] ?? null;
        $self->billingPricePerMonth = $data['billingPricePerMonth'] ?? 0;
        $self->billingName = $data['billingName'] ?? null;
        $self->billingEmail = $data['billingEmail'] ?? null;
        $self->billingAddressStreetLine1 = $data['billingAddressStreetLine1'] ?? null;
        $self->billingAddressStreetLine2 = $data['billingAddressStreetLine2'] ?? null;
        $self->billingAddressPostalCode = $data['billingAddressPostalCode'] ?? null;
        $self->billingAddressCity = $data['billingAddressCity'] ?? null;
        $self->billingAddressCountry = $data['billingAddressCountry'] ?? null;
        $self->billingTaxId = $data['billingTaxId'] ?? null;
        $self->textingSenderCode = $data['textingSenderCode'] ?? null;
        $self->emailThrottlingPerHour = $data['emailThrottlingPerHour'] ?? null;
        $self->setProjectsSlots($data['projectsSlots'] ?? 16);
        $self->addCredits($data['credits'] ?? 1000000);
        $self->addTextsCredits($data['textsCredits'] ?? 10);
        $self->setSubscriptionNotifications($data['subscriptionNotifications'] ?? new SubscriptionNotifications([]));

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        if (!($data['incomplete'] ?? false)) {
            $self->updateSubscriptionStatus(
                $data['trialing'] ?? false,
                $data['currentPeriodEnd'] ?? new \DateTime('+1 month')
            );
        }

        return $self;
    }

    /*
     * Setters
     */
    public function applyBillingDetailsUpdate(UpdateBillingDetailsData $data)
    {
        $this->billingName = $data->name;
        $this->billingEmail = $data->email;
        $this->billingAddressStreetLine1 = $data->streetLine1;
        $this->billingAddressStreetLine2 = $data->streetLine2;
        $this->billingAddressPostalCode = $data->postalCode;
        $this->billingAddressCity = Address::formatCityName($data->city);
        $this->billingAddressCountry = $data->country;
    }

    /*
     * Core properties
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function isDemo(): bool
    {
        return $this->isDemo;
    }

    public function isShowPreview(): bool
    {
        return $this->showPreview;
    }

    public function setShowPreview(bool $showPreview)
    {
        $this->showPreview = $showPreview;
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function getPartner(): ?User
    {
        return $this->partner;
    }

    public function setPartner(?User $partner)
    {
        $this->partner = $partner;
    }

    /*
     * CRM index
     */
    public function getCrmIndexName(): string
    {
        return CrmIndexer::getIndexName($this->uuid->toRfc4122(), $this->crmIndexVersion ?: '');
    }

    public function getCrmIndexVersion(): string
    {
        return $this->crmIndexVersion ?: '';
    }

    public function setCrmIndexVersion(string $crmIndexVersion)
    {
        $this->crmIndexVersion = $crmIndexVersion;
    }

    public function getCrmSearchKey(): string
    {
        return $this->crmSearchKey ?: '';
    }

    public function setCrmSearchKey(string $crmSearchKey)
    {
        $this->crmSearchKey = $crmSearchKey;
    }

    public function getCrmSearchKeyUid(): string
    {
        return $this->crmSearchKeyUid?->toRfc4122() ?: '';
    }

    public function setCrmSearchKeyUid(string $crmSearchKeyUid)
    {
        $this->crmSearchKeyUid = Uuid::fromString($crmSearchKeyUid);
    }

    /*
     * Subscription
     */
    public function setSubscriptionPlan(string $subscriptionPlan)
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function isFeatureInPlan(string $feature): bool
    {
        return Plans::isFeatureAccessibleFor($feature, $this);
    }

    public function updateSubscriptionStatus(bool $trialing, \DateTime $currentPeriodEnd)
    {
        $this->subscriptionTrialing = $trialing;
        $this->subscriptionCurrentPeriodEnd = $currentPeriodEnd;
    }

    public function setSubscriptionTrialing(bool $subscriptionTrialing)
    {
        $this->subscriptionTrialing = $subscriptionTrialing;
    }

    public function setSubscriptionCurrentPeriodEnd(?\DateTime $subscriptionCurrentPeriodEnd)
    {
        $this->subscriptionCurrentPeriodEnd = $subscriptionCurrentPeriodEnd;
    }

    public function getSubscriptionNotifications(): SubscriptionNotifications
    {
        return new SubscriptionNotifications($this->subscriptionNotifications ?: []);
    }

    public function setSubscriptionNotifications(SubscriptionNotifications $subscriptionNotifications)
    {
        $this->subscriptionNotifications = $subscriptionNotifications->toArray();
    }

    public function setProjectsSlots(int $amount)
    {
        if ($this->projectsSlots > $amount) {
            $this->log('projects_removed', ['%amount%' => $this->projectsSlots - $amount]);
        } elseif ($this->projectsSlots < $amount) {
            $this->log('projects_added', ['%amount%' => $amount - $this->projectsSlots]);
        }

        $this->projectsSlots = $amount;
    }

    public function setCreditsBalance(int $creditsBalance)
    {
        $this->creditsBalance = $creditsBalance;
    }

    public function addCredits(int $amount)
    {
        if ($amount < 0) {
            throw new \LogicException('Emails credits add amount cannot be null, '.$amount.' provided');
        }

        $this->creditsBalance += $amount;
        $this->log('credits_added', ['%amount%' => number_format($amount, 0, ',', ' ')]);
    }

    public function useCredits(int $amount, string $action)
    {
        if ($amount < 0) {
            throw new \LogicException('Emails credits usage amount cannot be null, '.$amount.' provided');
        }

        $this->creditsBalance -= $amount;
        $this->log('credits_used', ['%amount%' => number_format($amount, 0, ',', ' '), '%action%' => $action]);
    }

    public function setTextsCreditsBalance(int $textsCreditsBalance)
    {
        $this->textsCreditsBalance = $textsCreditsBalance;
    }

    public function addTextsCredits(int $amount)
    {
        if ($amount < 0) {
            throw new \LogicException('Texts credits add amount cannot be null, '.$amount.' provided');
        }

        $this->textsCreditsBalance += $amount;
        $this->log('texts_credits_added', ['%amount%' => number_format($amount, 0, ',', ' ')]);
    }

    public function useTextsCredits(int $amount, string $action)
    {
        if ($amount < 0) {
            throw new \LogicException('Texts credits usage amount cannot be null, '.$amount.' provided');
        }

        $this->textsCreditsBalance -= $amount;
        $this->log('texts_credits_used', ['%amount%' => number_format($amount, 0, ',', ' '), '%action%' => $action]);
    }

    private function log(string $message, array $context = [])
    {
        $this->subscriptionLogs->add(new SubscriptionLog($this, $message, $context));
    }

    public function getSubscriptionPlan(): string
    {
        return $this->subscriptionPlan;
    }

    /**
     * @return SubscriptionLog[]|Collection
     */
    public function getSubscriptionLogs()
    {
        return $this->subscriptionLogs;
    }

    public function getProjectsSlots(): int
    {
        return $this->projectsSlots;
    }

    public function getCreditsBalance(): int
    {
        return $this->creditsBalance;
    }

    public function getTextsCreditsBalance(): int
    {
        return $this->textsCreditsBalance;
    }

    public function isSubscriptionActive(): bool
    {
        return !$this->isSubscriptionExpired();
    }

    public function isSubscriptionExpired(): bool
    {
        return !$this->subscriptionCurrentPeriodEnd
            || $this->subscriptionCurrentPeriodEnd < new \DateTime('-1 day');
    }

    public function isSubscriptionTrialing(): bool
    {
        return $this->subscriptionTrialing;
    }

    public function getSubscriptionCurrentPeriodEnd(): ?\DateTime
    {
        return $this->subscriptionCurrentPeriodEnd;
    }

    /*
     * Billing
     */
    public function getBillingPricePerMonth(): ?int
    {
        return $this->billingPricePerMonth;
    }

    public function setBillingPricePerMonth(?int $billingPricePerMonth)
    {
        $this->billingPricePerMonth = $billingPricePerMonth;
    }

    public function getBillingName(): ?string
    {
        return $this->billingName;
    }

    public function setBillingName(?string $billingName)
    {
        $this->billingName = $billingName;
    }

    public function getBillingEmail(): ?string
    {
        return $this->billingEmail;
    }

    public function setBillingEmail(?string $billingEmail)
    {
        $this->billingEmail = $billingEmail;
    }

    public function getBillingAddressStreetLine1(): ?string
    {
        return $this->billingAddressStreetLine1;
    }

    public function setBillingAddressStreetLine1(?string $billingAddressStreetLine1)
    {
        $this->billingAddressStreetLine1 = $billingAddressStreetLine1;
    }

    public function getBillingAddressStreetLine2(): ?string
    {
        return $this->billingAddressStreetLine2;
    }

    public function setBillingAddressStreetLine2(?string $billingAddressStreetLine2)
    {
        $this->billingAddressStreetLine2 = $billingAddressStreetLine2;
    }

    public function getBillingAddressPostalCode(): ?string
    {
        return $this->billingAddressPostalCode;
    }

    public function setBillingAddressPostalCode(?string $billingAddressPostalCode)
    {
        $this->billingAddressPostalCode = $billingAddressPostalCode;
    }

    public function getBillingAddressCity(): ?string
    {
        return $this->billingAddressCity;
    }

    public function setBillingAddressCity(?string $billingAddressCity)
    {
        $this->billingAddressCity = Address::formatCityName($billingAddressCity);
    }

    public function getBillingAddressCountry(): ?string
    {
        return $this->billingAddressCountry;
    }

    public function setBillingAddressCountry(?string $billingAddressCountry)
    {
        $this->billingAddressCountry = $billingAddressCountry;
    }

    public function getBillingTaxId(): ?string
    {
        return $this->billingTaxId;
    }

    public function setBillingTaxId(?string $billingTaxId)
    {
        $this->billingTaxId = $billingTaxId;
    }

    public function getMollieCustomerId(): ?string
    {
        return $this->mollieCustomerId;
    }

    public function setMollieCustomerId(?string $mollieCustomerId)
    {
        $this->mollieCustomerId = $mollieCustomerId;
    }

    /*
     * Community configuration
     */
    public function setMainTagsIsProgress(bool $mainTagsIsProgress)
    {
        $this->mainTagsIsProgress = $mainTagsIsProgress;
    }

    public function setForceTwoFactorAuth(bool $forceTwoFactorAuth): void
    {
        $this->forceTwoFactorAuth = $forceTwoFactorAuth;
    }

    /*
     * Getters
     */

    public function hasForceTwoFactorAuth(): bool
    {
        return (bool) $this->forceTwoFactorAuth;
    }

    /**
     * @return OrganizationMainTag[]|Collection
     */
    public function getMainTags(): Collection
    {
        return $this->mainTags;
    }

    public function getMainTag(Tag $tag): ?OrganizationMainTag
    {
        /** @var OrganizationMainTag $mt */
        foreach ($this->mainTags as $mt) {
            if ($mt->getTag()->getId() === $tag->getId()) {
                return $mt;
            }
        }

        return null;
    }

    public function mainTagsIsProgress(): bool
    {
        return $this->mainTagsIsProgress;
    }

    public function getMainDomain(): ?Domain
    {
        $mainProject = null;

        // Find the oldest global project
        foreach ($this->projects as $project) {
            if (!$mainProject) {
                $mainProject = $project;
            }

            if ($mainProject->isLocal() && $project->isGlobal()) {
                $mainProject = $project;
            }

            if ($mainProject->isGlobal() && $project->isGlobal() && $mainProject->getCreatedAt() > $project->getCreatedAt()) {
                $mainProject = $project;
            }
        }

        return $mainProject ? $mainProject->getRootDomain() : null;
    }

    /*
     * Integrations
     */
    public function getQuorumToken(): ?string
    {
        return $this->quorumToken;
    }

    public function setQuorumToken(?string $quorumToken)
    {
        $this->quorumToken = $quorumToken;
    }

    public function getQuorumDefaultCity(): ?string
    {
        return $this->quorumDefaultCity;
    }

    public function setQuorumDefaultCity(?string $quorumDefaultCity)
    {
        $this->quorumDefaultCity = $quorumDefaultCity;
    }

    public function getSpallianEndpoint(): ?string
    {
        return $this->spallianEndpoint;
    }

    public function setSpallianEndpoint(?string $spallianEndpoint)
    {
        $this->spallianEndpoint = $spallianEndpoint;
    }

    /*
     * Specializations
     */
    public function getConsoleCustomCss(): ?string
    {
        return $this->consoleCustomCss;
    }

    public function setConsoleCustomCss(?string $consoleCustomCss)
    {
        $this->consoleCustomCss = $consoleCustomCss;
    }

    public function getTextingSenderCode(): ?string
    {
        return $this->textingSenderCode;
    }

    public function setTextingSenderCode(?string $textingSenderCode)
    {
        $this->textingSenderCode = $textingSenderCode;
    }

    /*
     * Email provider
     */

    public function getEmailEnableOpenTracking(): ?bool
    {
        return $this->emailEnableOpenTracking;
    }

    public function setEmailEnableOpenTracking(?bool $emailEnableOpenTracking): void
    {
        $this->emailEnableOpenTracking = $emailEnableOpenTracking;
    }

    public function getEmailEnableClickTracking(): ?bool
    {
        return $this->emailEnableClickTracking;
    }

    public function setEmailEnableClickTracking(?bool $emailEnableClickTracking): void
    {
        $this->emailEnableClickTracking = $emailEnableClickTracking;
    }

    public function getEmailThrottlingPerHour(): ?int
    {
        return $this->emailThrottlingPerHour;
    }

    public function setEmailThrottlingPerHour(?int $emailThrottlingPerHour): void
    {
        $this->emailThrottlingPerHour = $emailThrottlingPerHour;
    }

    public function getEmailProvider(): ?string
    {
        return $this->emailProvider;
    }

    public function setEmailProvider(?string $emailProvider): void
    {
        $this->emailProvider = $emailProvider;
    }

    public function getMailchimpServerPrefix(): ?string
    {
        return $this->mailchimpServerPrefix;
    }

    public function setMailchimpServerPrefix(?string $mailchimpServerPrefix): void
    {
        $this->mailchimpServerPrefix = $mailchimpServerPrefix;
    }

    public function getMailchimpApiKey(): ?string
    {
        return $this->mailchimpApiKey;
    }

    public function setMailchimpApiKey(?string $mailchimpApiKey): void
    {
        $this->mailchimpApiKey = $mailchimpApiKey;
    }

    public function getMailchimpAudienceName(): ?string
    {
        return $this->mailchimpAudienceName;
    }

    public function setMailchimpAudienceName(?string $mailchimpAudienceName): void
    {
        $this->mailchimpAudienceName = $mailchimpAudienceName;
    }

    /*
     * White label
     */

    public function applyWhiteLabelUpdate(?Upload $logo, ?string $name): void
    {
        $this->whiteLabelLogo = $logo ?: $this->whiteLabelLogo;
        $this->whiteLabelName = $name;
    }

    public function getWhiteLabelLogo(): ?Upload
    {
        return $this->whiteLabelLogo;
    }

    public function getWhiteLabelName(): ?string
    {
        return $this->whiteLabelName;
    }

    /*
     * Relationships
     */

    /**
     * @return Collection<Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /**
     * @return Collection<Domain>
     */
    public function getDomains(): Collection
    {
        return $this->domains;
    }

    /**
     * @return Collection<OrganizationMember>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * @return Collection<OrganizationMember>
     */
    public function getAdmins(): Collection
    {
        $admins = [];
        foreach ($this->members as $member) {
            if ($member->isAdmin()) {
                $admins[] = $member->getMember();
            }
        }

        return new ArrayCollection($admins);
    }

    /**
     * @param Collection|Project[] $projects
     *
     * @return Collection|Project[]
     */
    public function filterAccessibleProjects(iterable $projects, OrganizationMember|Registration|null $collaborator): Collection
    {
        $projects = is_array($projects) ? $projects : iterator_to_array($projects);

        $configuredProjectsIds = [];
        $isAdmin = false;

        if ($collaborator instanceof Registration) {
            $configuredProjectsIds = $collaborator->getConfiguredProjectsIds();
            $isAdmin = $collaborator->isAdmin();
        } elseif ($collaborator instanceof OrganizationMember) {
            $configuredProjectsIds = $collaborator->getProjectsPermissions()->getConfiguredProjectsIds();
            $isAdmin = $collaborator->isAdmin();
        }

        if ($isAdmin) {
            return new ArrayCollection($projects);
        }

        if (!$configuredProjectsIds) {
            return new ArrayCollection([]);
        }

        // If there is at least one permission on a project, it's accessible
        $accessible = new ArrayCollection([]);
        foreach ($projects as $project) {
            if (in_array($project->getUuid()->toRfc4122(), $configuredProjectsIds, true)) {
                $accessible->add($project);
            }
        }

        return $accessible;
    }

    /**
     * @return Collection|Registration[]
     */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /**
     * @return Collection|WebsiteTheme[]
     */
    public function getWebsiteThemes(): Collection
    {
        return $this->websiteThemes;
    }
}
