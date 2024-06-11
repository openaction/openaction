<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Form\Website\Model\EventData;
use App\Repository\Website\EventRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table('website_events')]
class Event implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityProjectTrait;
    use Util\EntityMemberRestrictedTrait;
    use Util\EntityPageViewsTrait;
    use Util\EntityExternalUrlTrait;

    #[ORM\Column(length: 200)]
    private string $title;

    #[ORM\Column(length: 200)]
    private ?string $slug = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = '';

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $publishedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $beginAt = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $timezone = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 35, nullable: true)]
    private ?string $buttonText = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $address = null;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $image = null;

    /**
     * @var EventCategory[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: EventCategory::class, inversedBy: 'events')]
    #[ORM\JoinTable(name: 'website_events_events_categories')]
    private Collection $categories;

    #[ORM\ManyToOne(targetEntity: Form::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Form $form = null;

    /**
     * @var Collection<TrombinoscopePerson>
     */
    #[ORM\ManyToMany(targetEntity: TrombinoscopePerson::class, inversedBy: 'events')]
    #[ORM\JoinTable(name: 'website_events_participants')]
    #[ORM\OrderBy(['weight' => 'ASC'])]
    private Collection $participants;

    public function __construct(Project $project, string $title)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->uuid = Uid::random();
        $this->title = $title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
        $this->categories = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    /*
     * Factories
     */

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['title']);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['title']);
        $self->content = $data['content'] ?? '';
        $self->publishedAt = $data['publishedAt'] ?? null;
        $self->beginAt = $data['beginAt'] ?? null;
        $self->timezone = $data['timezone'] ?? 'Europe/Paris';
        $self->url = $data['url'] ?? null;
        $self->buttonText = $data['buttonText'] ?? null;
        $self->latitude = $data['latitude'] ?? null;
        $self->longitude = $data['longitude'] ?? null;
        $self->address = $data['address'] ?? null;
        $self->image = $data['image'] ?? null;
        $self->externalUrl = $data['externalUrl'] ?? null;
        $self->pageViews = $data['pageViews'] ?? 0;
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;
        $self->form = $data['form'] ?? null;

        if ($data['slug'] ?? null) {
            $self->slug = $data['slug'];
        }

        foreach ($data['categories'] ?? [] as $category) {
            $self->addCategory($category);
        }

        foreach ($data['participants'] ?? [] as $participant) {
            $self->participants[] = $participant;
        }

        return $self;
    }

    public function duplicate(): self
    {
        $self = new self($this->project, $this->title);
        $self->slug = $this->slug;
        $self->content = $this->content;
        $self->beginAt = $this->beginAt;
        $self->timezone = $this->timezone;
        $self->url = $this->url;
        $self->buttonText = $this->buttonText;
        $self->latitude = $this->latitude;
        $self->longitude = $this->longitude;
        $self->address = $this->address;
        $self->onlyForMembers = $this->onlyForMembers;

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'event';
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
        return $this->title;
    }

    public function getSearchContent(): ?string
    {
        return strip_tags($this->content);
    }

    public function getSearchCategoriesFacet(): array
    {
        return $this->categories->map(static fn (EventCategory $c) => $c->getName())->toArray();
    }

    public function getSearchStatusFacet(): string
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
            'content' => $this->content,
            'address' => $this->address,
            'image' => $this->image?->getPathname(),
        ];
    }

    /*
     * Setters
     */
    public function setPublishedAt(\DateTime $date = null)
    {
        $this->publishedAt = $date;
    }

    public function applyContentUpdate(EventData $data)
    {
        $this->title = (string) $data->title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
        $this->beginAt = $data->beginAt;
        $this->timezone = $data->timezone;
        $this->content = (string) $data->content;
        $this->url = (string) $data->url;
        $this->buttonText = (string) $data->buttonText;
        $this->address = (string) $data->address;
        $this->latitude = $data->latitude;
        $this->longitude = $data->longitude;
    }

    public function applyMetadataUpdate(EventData $data)
    {
        $this->publishedAt = $data->publishedAt ? new \DateTime($data->publishedAt) : null;
        $this->externalUrl = $data->externalUrl ?: null;
        $this->onlyForMembers = (bool) $data->onlyForMembers;
    }

    public function setImage(?Upload $image)
    {
        $this->image = $image;
    }

    public function setForm(?Form $form)
    {
        $this->form = $form;
    }

    /*
     * Getters
     */
    public function isPublished()
    {
        return $this->publishedAt && $this->publishedAt < new \DateTime();
    }

    public function isDraft()
    {
        return !$this->publishedAt;
    }

    /**
     * @return Collection|EventCategory[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(EventCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function getBeginAt(): ?\DateTime
    {
        return $this->beginAt;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getButtonText(): ?string
    {
        return $this->buttonText;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getImage(): ?Upload
    {
        return $this->image;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    /**
     * @return Collection<TrombinoscopePerson>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }
}
