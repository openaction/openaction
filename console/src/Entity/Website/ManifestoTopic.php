<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Form\Website\Model\ManifestoTopicData;
use App\Form\Website\Model\ManifestoTopicPublishedAtData;
use App\Repository\Website\ManifestoTopicRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ManifestoTopicRepository::class)]
#[ORM\Table('website_manifestos_topics')]
class ManifestoTopic implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityProjectTrait;
    use Util\EntityPageViewsTrait;

    #[ORM\Column(length: 200)]
    private string $title;

    #[ORM\Column(length: 200)]
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(length: 6)]
    private string $color = '000000';

    #[ORM\Column(type: 'smallint')]
    private int $weight;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $image = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $publishedAt = null;

    /**
     * @var ManifestoProposal[]|Collection
     */
    #[ORM\OneToMany(targetEntity: ManifestoProposal::class, mappedBy: 'topic', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['weight' => 'ASC'])]
    private Collection $proposals;

    public function __construct(Project $project, string $title, int $weight)
    {
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->weight = $weight;
        $this->proposals = new ArrayCollection();

        $this->populateTimestampable();
        $this->setTitle($title);
    }

    /*
     * Factories
     */

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['title'], $data['weight'] ?? 1);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['title']);
        $self->description = $data['description'] ?? null;
        $self->color = $data['color'] ?? '000000';
        $self->image = $data['image'] ?? null;
        $self->publishedAt = $data['publishedAt'] ?? null;
        $self->pageViews = $data['pageViews'] ?? 0;

        return $self;
    }

    public function duplicate(): self
    {
        $self = new self($this->project, $this->title, $this->weight + 1);
        $self->description = $this->description;
        $self->color = $this->color;

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'manifesto-topic';
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
        return $this->description;
    }

    public function getSearchCategoriesFacet(): array
    {
        return [];
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
            'description' => $this->description,
            'image' => $this->image?->getPathname(),
        ];
    }

    /*
     * Setters
     */
    public function setPublishedAt(?\DateTime $date = null)
    {
        $this->publishedAt = $date;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        $this->slug = (new AsciiSlugger())->slug($title)->lower();
    }

    public function setImage(?Upload $image)
    {
        $this->image = $image;
    }

    public function applyUpdate(ManifestoTopicData $data)
    {
        $this->setTitle($data->title ?: '');
        $this->color = $data->color ?: '000000';
        $this->description = $data->description;
    }

    public function applyPublicationUpdate(ManifestoTopicPublishedAtData $data)
    {
        $this->publishedAt = $data->publishedAt ? new \DateTime($data->publishedAt) : null;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getImage(): ?Upload
    {
        return $this->image;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function getProposals()
    {
        return $this->proposals;
    }
}
