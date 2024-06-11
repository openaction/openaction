<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Form\Website\Model\TrombinoscopePersonData;
use App\Repository\Website\TrombinoscopePersonRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TrombinoscopePersonRepository::class)]
#[ORM\Table('website_trombinoscope_persons')]
class TrombinoscopePerson implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityPageViewsTrait;

    #[ORM\Column(length: 100)]
    private string $fullName;

    #[ORM\Column(length: 100)]
    private ?string $slug;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\Column(type: 'integer')]
    private ?int $weight = null;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $image = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialEmail;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialWebsite;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialFacebook;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialTwitter;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialInstagram;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialLinkedIn;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialYoutube;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $socialMedium;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $socialTelegram;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $publishedAt = null;

    /**
     * @var Collection<TrombinoscopeCategory>
     */
    #[ORM\ManyToMany(targetEntity: TrombinoscopeCategory::class, inversedBy: 'persons')]
    #[ORM\JoinTable(name: 'website_trombinoscope_persons_categories')]
    private Collection $categories;

    /**
     * @var Collection<Post>
     */
    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'authors')]
    #[ORM\JoinTable(name: 'website_posts_authors')]
    #[ORM\OrderBy(['publishedAt' => 'DESC'])]
    private Collection $posts;

    /**
     * @var Collection<Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participants')]
    #[ORM\JoinTable(name: 'website_events_participants')]
    #[ORM\OrderBy(['publishedAt' => 'DESC'])]
    private Collection $events;

    public function __construct(Project $project, string $fullName, int $weight)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->uuid = Uid::random();
        $this->fullName = $fullName;
        $this->slug = (new AsciiSlugger())->slug($this->fullName)->lower();
        $this->weight = $weight;
        $this->categories = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    /*
     * Factories
     */

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['fullName'], $data['weight'] ?? 1);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['fullName']);
        $self->content = $data['content'] ?? '';
        $self->role = $data['role'] ?? null;
        $self->image = $data['image'] ?? null;
        $self->publishedAt = $data['publishedAt'] ?? null;
        $self->socialEmail = $data['socialEmail'] ?? null;
        $self->socialWebsite = $data['socialWebsite'] ?? null;
        $self->socialFacebook = $data['socialFacebook'] ?? null;
        $self->socialTwitter = $data['socialTwitter'] ?? null;
        $self->socialInstagram = $data['socialInstagram'] ?? null;
        $self->socialLinkedIn = $data['socialLinkedIn'] ?? null;
        $self->socialYoutube = $data['socialYoutube'] ?? null;
        $self->socialMedium = $data['socialMedium'] ?? null;
        $self->socialTelegram = $data['socialTelegram'] ?? null;

        foreach ($data['categories'] ?? [] as $category) {
            $self->categories[] = $category;
        }

        return $self;
    }

    public function duplicate(): self
    {
        $self = new self($this->project, $this->fullName, $this->weight);
        $self->slug = $this->slug;
        $self->role = $this->role;
        $self->description = $this->description;
        $self->content = $this->content;
        $self->socialWebsite = $this->socialWebsite;
        $self->socialEmail = $this->socialEmail;
        $self->socialFacebook = $this->socialEmail;
        $self->socialTwitter = $this->socialEmail;
        $self->socialInstagram = $this->socialEmail;
        $self->socialLinkedIn = $this->socialEmail;
        $self->socialYoutube = $this->socialEmail;
        $self->socialMedium = $this->socialEmail;
        $self->socialTelegram = $this->socialEmail;

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'trombinoscope-person';
    }

    public function isSearchPublic(): bool
    {
        return $this->isPublished();
    }

    public function getSearchUuid(): string
    {
        return $this->uuid->toRfc4122();
    }

    public function getSearchTitle(): string
    {
        return $this->fullName;
    }

    public function getSearchContent(): ?string
    {
        return strip_tags($this->content);
    }

    public function getSearchCategoriesFacet(): array
    {
        return $this->categories->map(static fn (TrombinoscopeCategory $c) => $c->getName())->toArray();
    }

    public function getSearchStatusFacet(): ?string
    {
        if ($this->isPublished()) {
            return 'published';
        }

        if ($this->publishedAt) {
            return 'scheduled';
        }

        return 'draft';
    }

    public function getSearchAreaTreeFacet(): array
    {
        return [];
    }

    public function getSearchDateFacet(): ?int
    {
        return (int) $this->publishedAt?->format('U');
    }

    public function getSearchMetadata(): array
    {
        return [
            'project' => $this->project->getUuid()->toRfc4122(),
            'projectName' => $this->project->getName(),
            'slug' => $this->slug,
            'role' => $this->role,
            'description' => $this->description,
            'image' => $this->image?->getPathname(),
        ];
    }

    /*
     * Setters
     */
    public function setPublishedAt(\DateTime $date = null): void
    {
        $this->publishedAt = $date;
    }

    public function applyContentUpdate(TrombinoscopePersonData $data): void
    {
        $this->fullName = (string) $data->fullName;
        $this->slug = (new AsciiSlugger())->slug($this->fullName)->lower();
        $this->content = (string) $data->content;
    }

    public function applyMetadataUpdate(TrombinoscopePersonData $data): void
    {
        $this->role = (string) $data->role;
        $this->description = (string) $data->description;
        $this->publishedAt = $data->publishedAt ? new \DateTime($data->publishedAt) : null;
        $this->socialWebsite = (string) $data->socialWebsite;
        $this->socialEmail = (string) $data->socialEmail;
        $this->socialFacebook = (string) $data->socialFacebook;
        $this->socialTwitter = (string) $data->socialTwitter;
        $this->socialInstagram = (string) $data->socialInstagram;
        $this->socialLinkedIn = (string) $data->socialLinkedIn;
        $this->socialYoutube = (string) $data->socialYoutube;
        $this->socialMedium = (string) $data->socialMedium;
        $this->socialTelegram = (string) $data->socialTelegram;
    }

    public function setImage(?Upload $image): void
    {
        $this->image = $image;
    }

    /*
     * Getters
     */
    public function isPublished(): bool
    {
        return $this->publishedAt && $this->publishedAt <= new \DateTime();
    }

    public function isDraft(): bool
    {
        return !$this->publishedAt;
    }

    /**
     * @return Collection<TrombinoscopeCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return Collection<Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function getImage(): ?Upload
    {
        return $this->image;
    }

    public function getSocialWebsite(): ?string
    {
        return $this->socialWebsite;
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

    public function getSocialTelegram(): ?string
    {
        return $this->socialTelegram;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @return Collection<Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }
}
