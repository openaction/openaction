<?php

namespace App\Entity\Theme;

use App\Entity\Organization;
use App\Entity\Upload;
use App\Entity\User;
use App\Entity\Util;
use App\Platform\Themes;
use App\Repository\Theme\WebsiteThemeRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WebsiteThemeRepository::class)]
#[ORM\Table('website_themes')]
#[ORM\Index(name: 'website_themes_installation_id', columns: ['installation_id'])]
#[ORM\Index(name: 'website_themes_repository_node_id', columns: ['repository_node_id'])]
class WebsiteTheme
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $author = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $installationId;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $repositoryNodeId;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $repositoryFullName;

    #[ORM\Column(type: 'boolean')]
    private bool $isUpdating = true;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $updateError = null;

    #[ORM\Column(type: 'json')]
    private array $name = [];

    #[ORM\Column(type: 'json')]
    private array $description = [];

    #[ORM\Column(type: 'json')]
    private array $defaultColors = [
        'primary' => Themes::DEFAULT_COLOR_PRIMARY,
        'secondary' => Themes::DEFAULT_COLOR_SECONDARY,
        'third' => Themes::DEFAULT_COLOR_THIRD,
    ];

    #[ORM\Column(type: 'json')]
    private array $defaultFonts = [
        'title' => Themes::DEFAULT_FONT_TITLE,
        'text' => Themes::DEFAULT_FONT_TEXT,
    ];

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $thumbnail = null;

    #[ORM\Column(type: 'json')]
    private array $templates = [];

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $postsPerPage = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $eventsPerPage = null;

    #[ORM\OneToMany(targetEntity: WebsiteThemeAsset::class, mappedBy: 'theme', orphanRemoval: true)]
    private Collection $assets;

    /**
     * @var Collection|Organization[]
     */
    #[ORM\ManyToMany(targetEntity: Organization::class, inversedBy: 'websiteThemes', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'organizations_website_themes')]
    private Collection $forOrganizations;

    public function __construct(?string $installationId, ?string $repositoryNodeId, ?string $repositoryFullName)
    {
        $this->uuid = Uid::random();
        $this->installationId = $installationId;
        $this->repositoryNodeId = $repositoryNodeId;
        $this->repositoryFullName = $repositoryFullName;
        $this->assets = new ArrayCollection();
        $this->forOrganizations = new ArrayCollection();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['installationId'], $data['repositoryNodeId'], $data['repositoryFullName']);
        $self->author = $data['author'] ?? null;
        $self->forOrganizations = new ArrayCollection($data['forOrganizations'] ?? []);
        $self->name = $data['name'] ?? ['fr' => 'Audacieux', 'en' => 'Bold'];
        $self->description = $data['description'] ?? ['fr' => 'Description Audacieux', 'en' => 'Description Bold'];
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->updatedAt = $data['updatedAt'] ?? new \DateTime();
        $self->isUpdating = $data['isUpdate'] ?? false;
        $self->updateError = $data['updateError'] ?? null;
        $self->templates = $data['templates'] ?? [];
        $self->defaultColors = $data['defaultColors'] ?? $self->defaultColors;
        $self->defaultFonts = $data['defaultFonts'] ?? $self->defaultFonts;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    /*
     * Setters
     */
    public function setAuthor(?User $author)
    {
        $this->author = $author;
    }

    public function setForOrganizations(Collection $forOrganizations)
    {
        $this->forOrganizations = $forOrganizations;
    }

    public function archive()
    {
        $this->installationId = null;
        $this->repositoryNodeId = null;
        $this->repositoryFullName = null;
    }

    public function setIsUpdating(bool $isUpdating)
    {
        $this->isUpdating = $isUpdating;
    }

    public function setUpdateError(?string $error)
    {
        $this->updateError = $error;
    }

    public function updateDetails(array $name, array $description, ?Upload $thumbnail, array $templates)
    {
        $this->name = $name;
        $this->description = $description;
        $this->templates = $templates;

        if ($thumbnail) {
            $this->thumbnail = $thumbnail;
        }
    }

    public function updateDefaultColors(string $primary, string $secondary, string $third)
    {
        $this->defaultColors = [
            'primary' => $primary,
            'secondary' => $secondary,
            'third' => $third,
        ];
    }

    public function updateDefaultFonts(string $title, string $text)
    {
        $this->defaultFonts = [
            'title' => $title,
            'text' => $text,
        ];
    }

    public function setPostsPerPage(?int $postsPerPage): void
    {
        $this->postsPerPage = $postsPerPage;
    }

    public function setEventsPerPage(?int $eventsPerPage): void
    {
        $this->eventsPerPage = $eventsPerPage;
    }

    /*
     * Getters
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function getInstallationId(): ?string
    {
        return $this->installationId;
    }

    public function getRepositoryNodeId(): ?string
    {
        return $this->repositoryNodeId;
    }

    public function getRepositoryFullName(): ?string
    {
        return $this->repositoryFullName;
    }

    public function isUpdating(): bool
    {
        return $this->isUpdating;
    }

    public function getUpdateError(): ?string
    {
        return $this->updateError;
    }

    public function getName(): array
    {
        return $this->name;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function getDefaultColors(): array
    {
        return $this->defaultColors;
    }

    public function getDefaultFonts(): array
    {
        return $this->defaultFonts;
    }

    public function getThumbnail(): ?Upload
    {
        return $this->thumbnail;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function getPostsPerPage(): ?int
    {
        return $this->postsPerPage;
    }

    public function getEventsPerPage(): ?int
    {
        return $this->eventsPerPage;
    }

    /**
     * @return Collection|WebsiteThemeAsset[]
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    /**
     * @return Collection|Organization[]
     */
    public function getForOrganizations(): Collection
    {
        return $this->forOrganizations;
    }

    public function getForOrganizationsNames(): array
    {
        $names = [];
        foreach ($this->forOrganizations as $orga) {
            $names[] = $orga->getName();
        }

        return $names;
    }
}
