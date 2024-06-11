<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Form\Website\Model\PostData;
use App\Repository\Website\PostRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table('website_posts')]
class Post implements Searchable
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
    private ?string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $quote = null;

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $video = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $publishedAt = null;

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $image = null;

    /**
     * @var Collection<PostCategory>
     */
    #[ORM\ManyToMany(targetEntity: PostCategory::class, inversedBy: 'posts')]
    #[ORM\JoinTable(name: 'website_posts_posts_categories')]
    private Collection $categories;

    /**
     * @var Collection<TrombinoscopePerson>
     */
    #[ORM\ManyToMany(targetEntity: TrombinoscopePerson::class, inversedBy: 'posts')]
    #[ORM\JoinTable(name: 'website_posts_authors')]
    #[ORM\OrderBy(['weight' => 'ASC'])]
    private Collection $authors;

    public function __construct(Project $project, string $title)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->uuid = Uid::random();
        $this->title = $title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
        $this->categories = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    /*
     * Factories
     */

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['title']);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['title']);
        $self->quote = $data['quote'] ?? null;
        $self->content = $data['content'] ?? '';
        $self->description = $data['description'] ?? null;
        $self->externalUrl = $data['externalUrl'] ?? null;
        $self->image = $data['image'] ?? null;
        $self->video = $data['video'] ?? null;
        $self->publishedAt = $data['publishedAt'] ?? null;
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;
        $self->pageViews = $data['pageViews'] ?? 0;

        foreach ($data['categories'] ?? [] as $category) {
            $self->categories[] = $category;
        }

        foreach ($data['authors'] ?? [] as $author) {
            $self->authors[] = $author;
        }

        return $self;
    }

    public function duplicate(bool $withCategories = true): self
    {
        $self = new self($this->project, $this->title);
        $self->description = $this->description;
        $self->quote = $this->quote;
        $self->content = $this->content;
        $self->video = $this->video;
        $self->onlyForMembers = $this->onlyForMembers;

        if ($withCategories) {
            foreach ($this->categories as $category) {
                $self->categories[] = $category;
            }
        }

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'post';
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
        return $this->categories->map(static fn (PostCategory $c) => $c->getName())->toArray();
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
            'quote' => $this->quote,
            'video' => $this->video,
            'image' => $this->image?->getPathname(),
        ];
    }

    /*
     * Setters
     */

    /**
     * @param iterable<PageCategory> $categories
     */
    public function applyAdminApiUpdate(
        string $content,
        ?string $description,
        ?string $video,
        ?string $quote,
        ?\DateTime $publishedAt,
        iterable $categories,
    ): void {
        $this->content = $content;
        $this->description = $description;
        $this->video = $video;
        $this->quote = $quote;
        $this->publishedAt = $publishedAt;

        foreach ($categories as $category) {
            $this->categories->add($category);
        }
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $date = null): void
    {
        $this->publishedAt = $date;
    }

    public function applyContentUpdate(PostData $data): void
    {
        $this->title = (string) $data->title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
        $this->content = (string) $data->content;
    }

    public function applyMetadataUpdate(PostData $data): void
    {
        $this->video = $data->video;
        $this->description = (string) $data->description;
        $this->publishedAt = $data->publishedAt ? new \DateTime($data->publishedAt) : null;
        $this->quote = (string) $data->quote;
        $this->externalUrl = $data->externalUrl ?: null;
        $this->onlyForMembers = (bool) $data->onlyForMembers;
    }

    public function setImage(?Upload $image): void
    {
        $this->image = $image;
    }

    public function isPublished(): bool
    {
        return $this->publishedAt && $this->publishedAt < new \DateTime();
    }

    public function isDraft(): bool
    {
        return !$this->publishedAt;
    }

    /**
     * @return Collection<PostCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return Collection<TrombinoscopePerson>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    /*
     * Getters
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function getImage(): ?Upload
    {
        return $this->image;
    }
}
