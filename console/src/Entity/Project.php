<?php

namespace App\Entity;

use App\Entity\Community\Tag;
use App\Entity\Model\ProjectMembershipFormSettings;
use App\Entity\Model\ProjectTerminology;
use App\Entity\Model\SocialSharers;
use App\Entity\Theme\WebsiteTheme;
use App\Form\Appearance\Model\LogosData;
use App\Form\Appearance\Model\WebsiteAccessData;
use App\Form\Appearance\Model\WebsiteIntroData;
use App\Form\Appearance\Model\WebsiteThemeData;
use App\Form\Developer\Model\UpdateCaptchaData;
use App\Form\Project\Model\UpdateDetailsData;
use App\Form\Project\Model\UpdateLegalitiesData;
use App\Form\Project\Model\UpdateMetasData;
use App\Form\Project\Model\UpdateSocialAccountsData;
use App\Platform\Features;
use App\Platform\Plans;
use App\Repository\ProjectRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table('projects')]
class Project implements UserInterface
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    /**
     * The areas of this project: some modules uses this field to
     * limit the organization database view to the concerned areas
     * (adherents, newsletter, ...).
     */
    #[ORM\ManyToMany(targetEntity: 'Area', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'projects_areas')]
    private Collection $areas;

    /**
     * The tags of this project: some modules uses this field to
     * limit the organization database view to the concerned tags.
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'projects', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'projects_tags')]
    private Collection $tags;

    #[ORM\Column(length: 60)]
    private string $name;

    #[ORM\Column(length: 64, unique: true)]
    private string $apiToken;

    #[ORM\Column(length: 80, unique: true, nullable: true)]
    private ?string $adminApiToken = null;

    /**
     * Modules enabled for this project.
     *
     * @see \App\Platform\Features for available modules.
     */
    #[ORM\Column(type: 'simple_array')]
    private array $modules = [];

    /**
     * Tools enabled for the modules this project.
     *
     * @see \App\Platform\Features for available tools for each module.
     */
    #[ORM\Column(type: 'simple_array')]
    private array $tools = [];

    #[ORM\ManyToOne(targetEntity: Domain::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private Domain $rootDomain;

    #[ORM\ManyToOne(targetEntity: Domain::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Domain $emailingDomain = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $subDomain = null;

    #[ORM\Column(length: 10)]
    private string $appearancePrimary;

    #[ORM\Column(length: 10)]
    private string $appearanceSecondary;

    #[ORM\Column(length: 10)]
    private string $appearanceThird;

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $appearanceLogoDark = null;

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $appearanceLogoWhite = null;

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $appearanceIcon = null;

    #[ORM\Column(type: 'json')]
    private array $appearanceTerminology;

    #[ORM\ManyToOne(targetEntity: WebsiteTheme::class)]
    private WebsiteTheme $websiteTheme;

    #[ORM\Column(length: 10)]
    private string $websiteLocale;

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $websiteSharer = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $websiteMetaTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $websiteMetaDescription = null;

    #[ORM\Column(length: 100)]
    private string $websiteFontTitle;

    #[ORM\Column(length: 100)]
    private string $websiteFontText;

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $websiteMainImage = null;

    #[ORM\OneToOne(targetEntity: Upload::class)]
    private ?Upload $websiteMainVideo = null;

    #[ORM\Column(length: 20)]
    private string $websiteMainIntroPosition = 'right';

    #[ORM\Column(type: 'boolean')]
    private bool $websiteMainIntroOverlay = true;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $websiteMainIntroTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $websiteMainIntroContent = null;

    #[ORM\Column(type: 'boolean')]
    private bool $websiteAnimateElements = true;

    #[ORM\Column(type: 'boolean')]
    private bool $websiteAnimateLinks = true;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $websiteAccessUser = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $websiteAccessPass = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $websiteCustomCss = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $websiteCustomJs = null;

    #[ORM\Column(type: 'json')]
    private array $websiteCustomTemplates = [];

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $websiteTurnstileSiteKey = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $websiteTurnstileSecretKey = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $websiteDisableGdprFields = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $emailingCustomCss = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $emailingLegalities = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $membershipMainPage = null;

    #[ORM\Column(type: 'json')]
    private array $membershipFormSettings;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialEmail = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $socialPhone = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialFacebook = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialTwitter = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialInstagram = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialLinkedIn = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialYoutube = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialMedium = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $socialSnapchat = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $socialTelegram = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialWhatsapp = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialTiktok = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialThreads = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialBluesky = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialMastodon = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $socialSharers = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $legalGdprName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $legalGdprEmail = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $legalGdprAddress = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $legalPublisherName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $legalPublisherRole = null;

    public function __construct(Organization $organization, string $name, WebsiteTheme $websiteTheme, string $locale = 'fr')
    {
        $this->populateTimestampable();
        $this->generateAdminApiToken();
        $this->uuid = Uid::random();
        $this->organization = $organization;
        $this->name = $name;
        $this->websiteLocale = $locale;
        $this->apiToken = bin2hex(random_bytes(32));
        $this->appearanceTerminology = (new ProjectTerminology([]))->toArray();
        $this->membershipFormSettings = (new ProjectMembershipFormSettings([]))->toArray();
        $this->areas = new ArrayCollection();
        $this->tags = new ArrayCollection();

        $this->emailingLegalities = <<<EOT
<p class="text-center footer-legalities">
    {{ 'community.footer.legalities'|trans({ '%email%': contact_email }, 'emails', project_locale) }}

    {% if website_enabled %}
        <br />
        <br />
        {{ 'community.footer.policy'|trans({}, 'emails', project_locale) }}
        <a href="{{ website_url }}" target="_blank">{{ website_url }}</a>
    {% endif %}
</p>

<p class="text-center">
    {{ 'community.footer.unsubscribe.label'|trans({ '%name%': organization_name }, 'emails', project_locale) }}
    <br />
    <a href="{{ gdpr_manage_url }}" target="_blank">
        {{ 'community.footer.unsubscribe.link'|trans({}, 'emails', project_locale) }}
    </a>
</p>
EOT;

        $this->changeWebsiteTheme($websiteTheme);
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['orga'], $data['name'] ?? 'Example project', $data['theme'] ?? '');
        $self->apiToken = $data['apiToken'] ?? $self->apiToken;
        $self->adminApiToken = $data['adminApiToken'] ?? $self->adminApiToken;
        $self->modules = $data['modules'] ?? Features::allModules();
        $self->tools = $data['tools'] ?? Features::allTools();
        $self->rootDomain = $data['domain'] ?? 'example';
        $self->subDomain = $data['subdomain'] ?? null;
        $self->websiteLocale = $data['websiteLocale'] ?? 'fr';
        $self->socialEmail = $data['socialEmail'] ?? null;
        $self->socialFacebook = $data['socialFacebook'] ?? null;
        $self->socialTwitter = $data['socialTwitter'] ?? null;
        $self->socialTelegram = $data['socialTelegram'] ?? null;
        $self->socialSnapchat = $data['socialSnapchat'] ?? null;
        $self->legalGdprName = $data['legalGdprName'] ?? null;
        $self->legalGdprEmail = $data['legalGdprEmail'] ?? null;
        $self->legalGdprAddress = $data['legalGdprAddress'] ?? null;
        $self->legalPublisherName = $data['legalPublisherName'] ?? null;
        $self->legalPublisherRole = $data['legalPublisherRole'] ?? null;
        $self->membershipMainPage = $data['membershipMainPage'] ?? null;
        $self->emailingLegalities = $data['emailingLegalities'] ?? null;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        if ($data['area'] ?? null) {
            $self->areas->add($data['area']);
        }

        /** @var Tag $tag */
        foreach ($data['tags'] ?? [] as $tag) {
            $self->tags->add($tag);
            $tag->getProjects()->add($self);
        }

        if (isset($data['socialSharers'])) {
            $self->applySocialSharersUpdate(new SocialSharers($data['socialSharers']));
        }

        return $self;
    }

    public function duplicate(): self
    {
        $self = new self($this->organization, $this->name.' (Copy)', $this->websiteTheme, $this->websiteLocale);
        $self->modules = $this->modules;
        $self->tools = $this->tools;
        $self->appearancePrimary = $this->appearancePrimary;
        $self->appearanceSecondary = $this->appearanceSecondary;
        $self->appearanceThird = $this->appearanceThird;
        $self->appearanceTerminology = $this->appearanceTerminology;
        $self->websiteMetaTitle = $this->websiteMetaTitle;
        $self->websiteMetaDescription = $this->websiteMetaDescription;
        $self->websiteFontTitle = $this->websiteFontTitle;
        $self->websiteFontText = $this->websiteFontText;
        $self->websiteMainIntroOverlay = $this->websiteMainIntroOverlay;
        $self->websiteMainIntroTitle = $this->websiteMainIntroTitle;
        $self->websiteMainIntroContent = $this->websiteMainIntroContent;
        $self->websiteAnimateElements = $this->websiteAnimateElements;
        $self->websiteAnimateLinks = $this->websiteAnimateLinks;
        $self->websiteAccessUser = $this->websiteAccessUser;
        $self->websiteAccessPass = $this->websiteAccessPass;
        $self->websiteCustomCss = $this->websiteCustomCss;
        $self->websiteCustomJs = $this->websiteCustomJs;
        $self->websiteCustomTemplates = $this->websiteCustomTemplates;
        $self->emailingCustomCss = $this->emailingCustomCss;
        $self->membershipMainPage = $this->membershipMainPage;
        $self->membershipFormSettings = $this->membershipFormSettings;
        $self->socialEmail = $this->socialEmail;
        $self->socialPhone = $this->socialPhone;
        $self->socialFacebook = $this->socialFacebook;
        $self->socialTwitter = $this->socialTwitter;
        $self->socialTelegram = $this->socialTelegram;
        $self->socialSnapchat = $this->socialSnapchat;
        $self->socialWhatsapp = $this->socialWhatsapp;
        $self->socialTiktok = $this->socialTiktok;
        $self->socialThreads = $this->socialThreads;
        $self->socialBluesky = $this->socialBluesky;
        $self->socialMastodon = $this->socialMastodon;
        $self->socialSharers = $this->socialSharers;

        foreach ($this->getAreas() as $area) {
            $self->areas->add($area);
        }

        foreach ($this->getTags() as $tag) {
            $self->tags->add($tag);
            $tag->getProjects()->add($self);
        }

        return $self;
    }

    /*
     * Settings
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function applyDetailsUpdate(UpdateDetailsData $data)
    {
        $this->name = (string) $data->name;
        $this->websiteLocale = $data->locale;
    }

    public function updateModules(array $modules, array $tools)
    {
        $this->modules = array_unique(array_values($modules));
        $this->tools = array_unique(array_values($tools));
    }

    public function setName(?string $name)
    {
        $this->name = $name ?: '';
    }

    public function generateAdminApiToken(): void
    {
        if (!$this->getAdminApiToken()) {
            $this->adminApiToken = 'admin_'.bin2hex(random_bytes(32));
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken ?? null;
    }

    public function getAdminApiToken(): ?string
    {
        return $this->adminApiToken;
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function getTools(): array
    {
        return $this->tools;
    }

    public function getAccessibleTools(): array
    {
        $accessible = [];
        foreach ($this->tools as $tool) {
            if (Plans::isFeatureAccessibleFor($tool, $this->organization)) {
                $accessible[] = $tool;
            }
        }

        return $accessible;
    }

    public function isGlobal(): bool
    {
        return !$this->isLocal() && !$this->isThematic();
    }

    public function isLocal(): bool
    {
        return $this->areas->count() > 0;
    }

    public function isThematic(): bool
    {
        return $this->tags->count() > 0;
    }

    public function isModuleEnabled(string $module): bool
    {
        return \in_array($module, $this->modules, true);
    }

    public function isToolEnabled(string $tool): bool
    {
        return \in_array($tool, $this->tools, true);
    }

    public function isFeatureInPlan(string $tool): bool
    {
        return $this->organization->isFeatureInPlan($tool);
    }

    public function getAreasIds(): array
    {
        $ids = [];
        foreach ($this->getAreas() as $area) {
            $ids[] = $area->getId();
        }

        return $ids;
    }

    /**
     * @return Collection|Area[]
     */
    public function getAreas(): Collection
    {
        return $this->areas;
    }

    public function getTagsIds(): array
    {
        $ids = [];
        foreach ($this->getTags() as $tag) {
            $ids[] = $tag->getId();
        }

        return $ids;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /*
     * Domain
     */
    public function updateDomain(Domain $rootDomain, string $subDomain = null)
    {
        $this->rootDomain = $rootDomain;
        $this->subDomain = $subDomain;
    }

    public function getSubDomain(): ?string
    {
        return $this->subDomain;
    }

    public function setSubDomain(?string $subDomain)
    {
        $this->subDomain = $subDomain ?: null;
    }

    public function getRootDomain(): Domain
    {
        return $this->rootDomain;
    }

    public function setRootDomain(Domain $rootDomain)
    {
        $this->rootDomain = $rootDomain;
    }

    public function getEmailingDomain(): Domain
    {
        return $this->emailingDomain ?: $this->rootDomain;
    }

    public function setEmailingDomain(?Domain $emailingDomain)
    {
        $this->emailingDomain = $emailingDomain;
    }

    public function getFullDomain()
    {
        return ($this->subDomain ? $this->subDomain.'.' : '').$this->rootDomain->getName();
    }

    /*
     * Appearance
     */
    public function applyLogosUpdate(LogosData $data)
    {
        if ($data->appearanceLogoDarkUpload) {
            $this->appearanceLogoDark = $data->appearanceLogoDarkUpload;
        }

        if ($data->appearanceLogoWhiteUpload) {
            $this->appearanceLogoWhite = $data->appearanceLogoWhiteUpload;
        }

        if ($data->appearanceIconUpload) {
            $this->appearanceIcon = $data->appearanceIconUpload;
        }
    }

    public function getAppearanceTerminology(): ProjectTerminology
    {
        return new ProjectTerminology($this->appearanceTerminology);
    }

    public function setAppearanceTerminology(ProjectTerminology $terminology)
    {
        $this->appearanceTerminology = $terminology->toArray();
    }

    public function setAppearanceIcon(?Upload $appearanceIcon)
    {
        $this->appearanceIcon = $appearanceIcon;
    }

    public function getMembershipMainPage(): ?string
    {
        return $this->membershipMainPage;
    }

    public function setMembershipMainPage(?string $membershipMainPage)
    {
        $this->membershipMainPage = $membershipMainPage;
    }

    public function getMembershipFormSettings(): ProjectMembershipFormSettings
    {
        return new ProjectMembershipFormSettings($this->membershipFormSettings);
    }

    public function setMembershipFormSettings(ProjectMembershipFormSettings $membershipFormSettings)
    {
        $this->membershipFormSettings = $membershipFormSettings->toArray();
    }

    public function getAppearancePrimary(): string
    {
        return $this->appearancePrimary;
    }

    public function getAppearanceSecondary(): string
    {
        return $this->appearanceSecondary;
    }

    public function getAppearanceThird(): string
    {
        return $this->appearanceThird;
    }

    public function getAppearanceLogoDark(): ?Upload
    {
        return $this->appearanceLogoDark;
    }

    public function getAppearanceLogoWhite(): ?Upload
    {
        return $this->appearanceLogoWhite;
    }

    public function getAppearanceIcon(): ?Upload
    {
        return $this->appearanceIcon;
    }

    /*
     * Website
     */
    public function applyWebsiteIntroUpdate(WebsiteIntroData $data)
    {
        $this->websiteMainIntroTitle = $data->websiteMainIntroTitle;
        $this->websiteMainIntroContent = $data->websiteMainIntroContent;
    }

    public function setWebsiteMainImage(?Upload $websiteMainImage)
    {
        $this->websiteMainImage = $websiteMainImage;
    }

    public function setWebsiteMainVideo(?Upload $websiteMainVideo)
    {
        $this->websiteMainVideo = $websiteMainVideo;
    }

    public function applyWebsiteThemeUpdate(WebsiteThemeData $data)
    {
        $this->websiteMainIntroOverlay = $data->mainIntroOverlay;
        $this->websiteMainIntroPosition = (string) $data->mainIntroPosition;
        $this->websiteAnimateElements = $data->animateElements;
        $this->websiteAnimateLinks = $data->animateLinks;

        // When changing the theme, use theme defaults
        if (!$this->websiteTheme || $this->websiteTheme->getId() !== $data->theme->getId()) {
            $this->changeWebsiteTheme($data->theme);

            return;
        }

        // Otherwise, no theme change => apply requested theme changes
        $this->appearancePrimary = (string) $data->appearancePrimary;
        $this->appearanceSecondary = (string) $data->appearanceSecondary;
        $this->appearanceThird = (string) $data->appearanceThird;
        $this->websiteFontTitle = (string) $data->fontTitle;
        $this->websiteFontText = (string) $data->fontText;
    }

    public function changeWebsiteTheme(WebsiteTheme $theme)
    {
        $this->websiteTheme = $theme;
        $this->appearancePrimary = (string) $theme->getDefaultColors()['primary'];
        $this->appearanceSecondary = (string) $theme->getDefaultColors()['secondary'];
        $this->appearanceThird = (string) $theme->getDefaultColors()['third'];
        $this->websiteFontTitle = (string) $theme->getDefaultFonts()['title'];
        $this->websiteFontText = (string) $theme->getDefaultFonts()['text'];
    }

    public function applyWebsiteAccessUpdate(WebsiteAccessData $data)
    {
        $this->websiteAccessUser = $data->websiteAccessUser;
        $this->websiteAccessPass = $data->websiteAccessPass;
    }

    public function applyWebsiteCssUpdate(string $content)
    {
        $this->websiteCustomCss = $content;
    }

    public function applyWebsiteJsUpdate(string $content)
    {
        $this->websiteCustomJs = $content;
    }

    public function applyWebsiteTemplatesUpdate(string $filename, ?string $content)
    {
        $this->websiteCustomTemplates[$filename] = $content;
    }

    public function applyWebsiteTurnstileUpdate(UpdateCaptchaData $data)
    {
        $this->websiteTurnstileSiteKey = $data->siteKey ?: null;
        $this->websiteTurnstileSecretKey = $data->secretKey ?: null;
    }

    public function setWebsiteDisableGdprFields(?bool $websiteDisableGdprFields): void
    {
        $this->websiteDisableGdprFields = $websiteDisableGdprFields;
    }

    public function getWebsiteLocale(): string
    {
        return $this->websiteLocale;
    }

    public function getWebsiteTheme(): ?WebsiteTheme
    {
        return $this->websiteTheme;
    }

    public function getWebsiteFontTitle(): string
    {
        return $this->websiteFontTitle;
    }

    public function getWebsiteFontText(): string
    {
        return $this->websiteFontText;
    }

    public function getWebsiteSharer(): ?Upload
    {
        return $this->websiteSharer;
    }

    public function getWebsiteMetaTitle(): ?string
    {
        return $this->websiteMetaTitle;
    }

    public function getWebsiteMetaDescription(): ?string
    {
        return $this->websiteMetaDescription;
    }

    public function getWebsiteMainImage(): ?Upload
    {
        return $this->websiteMainImage;
    }

    public function getWebsiteMainVideo(): ?Upload
    {
        return $this->websiteMainVideo;
    }

    public function getWebsiteMainIntroPosition(): string
    {
        return $this->websiteMainIntroPosition;
    }

    public function hasWebsiteMainIntroOverlay(): bool
    {
        return $this->websiteMainIntroOverlay;
    }

    public function getWebsiteMainIntroTitle(): ?string
    {
        return $this->websiteMainIntroTitle;
    }

    public function getWebsiteMainIntroContent(): ?string
    {
        return $this->websiteMainIntroContent;
    }

    public function isWebsiteAnimateElements(): bool
    {
        return $this->websiteAnimateElements;
    }

    public function isWebsiteAnimateLinks(): bool
    {
        return $this->websiteAnimateLinks;
    }

    public function getWebsiteAccessUser(): ?string
    {
        return $this->websiteAccessUser;
    }

    public function getWebsiteAccessPass(): ?string
    {
        return $this->websiteAccessPass;
    }

    public function getWebsiteCustomCss(): ?string
    {
        return $this->websiteCustomCss;
    }

    public function getWebsiteCustomJs(): ?string
    {
        return $this->websiteCustomJs;
    }

    public function getWebsiteCustomTemplates(): array
    {
        return $this->websiteCustomTemplates;
    }

    public function getWebsiteTurnstileSiteKey(): ?string
    {
        return $this->websiteTurnstileSiteKey;
    }

    public function getWebsiteTurnstileSecretKey(): ?string
    {
        return $this->websiteTurnstileSecretKey;
    }

    public function getWebsiteDisableGdprFields(): bool
    {
        return $this->websiteDisableGdprFields ?: false;
    }

    /*
     * Emailing
     */
    public function applyEmailingCssUpdate(string $content)
    {
        $this->emailingCustomCss = $content;
    }

    public function getEmailingCustomCss(): ?string
    {
        return $this->emailingCustomCss;
    }

    public function applyEmailingLegalitiesUpdate(string $content)
    {
        $this->emailingLegalities = $content;
    }

    public function getEmailingLegalities(): ?string
    {
        return $this->emailingLegalities;
    }

    /*
     * Socials
     */
    public function applyMetasUpdate(UpdateMetasData $data)
    {
        $this->websiteMetaTitle = (string) $data->websiteMetaTitle;
        $this->websiteMetaDescription = (string) $data->websiteMetaDescription;
    }

    public function setWebsiteSharer(Upload $websiteSharer)
    {
        $this->websiteSharer = $websiteSharer;
    }

    public function applySocialsAccountsUpdate(UpdateSocialAccountsData $data)
    {
        $this->socialEmail = $data->email;
        $this->socialPhone = $data->phone;
        $this->socialFacebook = $data->facebook;
        $this->socialTwitter = $data->twitter;
        $this->socialInstagram = $data->instagram;
        $this->socialLinkedIn = $data->linkedIn;
        $this->socialYoutube = $data->youtube;
        $this->socialMedium = $data->medium;
        $this->socialTelegram = $data->telegram;
        $this->socialSnapchat = $data->snapchat;
        $this->socialWhatsapp = $data->whatsapp;
        $this->socialTiktok = $data->tiktok;
        $this->socialThreads = $data->threads;
        $this->socialBluesky = $data->bluesky;
        $this->socialMastodon = $data->mastodon;
    }

    public function applySocialSharersUpdate(SocialSharers $socialSharers)
    {
        $this->socialSharers = $socialSharers->toArray();
    }

    public function getSocialSharers(): SocialSharers
    {
        return new SocialSharers($this->socialSharers ?? []);
    }

    public function getSocialEmail(): ?string
    {
        return $this->socialEmail;
    }

    public function getSocialFacebook(): ?string
    {
        return $this->socialFacebook;
    }

    public function getSocialTwitter(): ?string
    {
        return $this->socialTwitter;
    }

    public function getSocialInstagram(): ?string
    {
        return $this->socialInstagram;
    }

    public function getSocialLinkedIn(): ?string
    {
        return $this->socialLinkedIn;
    }

    public function getSocialYoutube(): ?string
    {
        return $this->socialYoutube;
    }

    public function getSocialMedium(): ?string
    {
        return $this->socialMedium;
    }

    public function getSocialSnapchat(): ?string
    {
        return $this->socialSnapchat;
    }

    public function getSocialTelegram(): ?string
    {
        return $this->socialTelegram;
    }

    public function getSocialPhone(): ?string
    {
        return $this->socialPhone;
    }

    public function getSocialWhatsapp(): ?string
    {
        return $this->socialWhatsapp;
    }

    public function getSocialTiktok(): ?string
    {
        return $this->socialTiktok;
    }

    public function getSocialThreads(): ?string
    {
        return $this->socialThreads;
    }

    public function getSocialBluesky(): ?string
    {
        return $this->socialBluesky;
    }

    public function getSocialMastodon(): ?string
    {
        return $this->socialMastodon;
    }

    /*
     * Legal
     */
    public function applyLegalitiesUpdate(UpdateLegalitiesData $data)
    {
        $this->legalGdprName = $data->legalGdprName;
        $this->legalGdprEmail = $data->legalGdprEmail;
        $this->legalGdprAddress = $data->legalGdprAddress;
        $this->legalPublisherName = $data->legalPublisherName;
        $this->legalPublisherRole = $data->legalPublisherRole;
    }

    public function getLegalGdprName(): ?string
    {
        return $this->legalGdprName;
    }

    public function getLegalGdprEmail(): ?string
    {
        return $this->legalGdprEmail;
    }

    public function getLegalGdprAddress(): ?string
    {
        return $this->legalGdprAddress;
    }

    public function getLegalPublisherName(): ?string
    {
        return $this->legalPublisherName;
    }

    public function getLegalPublisherRole(): ?string
    {
        return $this->legalPublisherRole;
    }

    /*
     * API
     */
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_PROJECT'];
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->apiToken;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    }
}
