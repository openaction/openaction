<?php

namespace App\Entity\Website;

use App\Bridge\CitePolitique\Client\Model\Page as CitePolitiquePage;
use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Util;
use App\Form\Website\Model\PageData;
use App\Repository\Website\PageRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function Symfony\Component\String\u;

use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table('website_pages')]
class Page implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityProjectTrait;
    use Util\EntityMemberRestrictedTrait;
    use Util\EntityPageViewsTrait;

    #[ORM\Column(length: 200)]
    private string $title;

    #[ORM\Column(length: 200)]
    private ?string $slug;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\OneToOne(targetEntity: Upload::class, cascade: ['persist', 'remove'])]
    private ?Upload $image = null;

    /**
     * @var PageCategory[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: PageCategory::class, inversedBy: 'pages')]
    #[ORM\JoinTable(name: 'website_pages_pages_categories')]
    private Collection $categories;

    public function __construct(Project $project, string $title)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->uuid = Uid::random();
        $this->title = $title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
        $this->categories = new ArrayCollection();
    }

    /*
     * Factories
     */
    public static function importFromCitePolitique(Project $project, CitePolitiquePage $cpPage, string $builtContent): self
    {
        $self = new self($project, u($cpPage->getTitle())->slice(0, 150));
        $self->description = strip_tags(u($cpPage->getMetaDescription() ?: '')->slice(0, 150));
        $self->content = $builtContent;

        return $self;
    }

    public static function createDefaultPage(Project $project, string $title, string $content): self
    {
        $self = new self($project, $title);
        $self->content = $content;

        return $self;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['title']);
        $self->uuid = isset($data['uuid']) ? Uuid::fromString($data['uuid']) : Uid::fixed($data['title']);
        $self->content = $data['content'] ?? '';
        $self->description = $data['description'] ?? null;
        $self->image = $data['image'] ?? null;
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;
        $self->pageViews = $data['pageViews'] ?? 0;

        foreach ($data['categories'] ?? [] as $category) {
            $self->addCategory($category);
        }

        return $self;
    }

    public function duplicate(): self
    {
        $self = new self($this->project, $this->title);
        $self->content = $this->content;
        $self->description = $this->description;
        $self->onlyForMembers = $this->onlyForMembers;

        foreach ($this->categories as $category) {
            $self->addCategory($category);
        }

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'page';
    }

    public function isSearchPublic(): bool
    {
        return true;
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
        return $this->categories->map(static fn (PageCategory $c) => $c->getName())->toArray();
    }

    public function getSearchStatusFacet(): ?string
    {
        return null;
    }

    public function getSearchAreaTreeFacet(): array
    {
        return [];
    }

    public function getSearchDateFacet(): ?int
    {
        return (int) $this->createdAt->format('U');
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

    /**
     * @param iterable<PageCategory> $categories
     */
    public function applyAdminApiUpdate(string $content, ?string $description, iterable $categories): void
    {
        $this->content = $content;
        $this->description = $description;

        foreach ($categories as $category) {
            $this->categories->add($category);
        }
    }

    public function applyContentUpdate(PageData $data)
    {
        $this->title = (string) $data->title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
        $this->content = (string) $data->content;
    }

    public function applyMetadataUpdate(PageData $data)
    {
        $this->description = (string) $data->description;
        $this->onlyForMembers = (bool) $data->onlyForMembers;
    }

    public function setImage(?Upload $image)
    {
        $this->image = $image;
    }

    /**
     * @return Collection|PageCategory[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(PageCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    /*
     * Getters
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getImage(): ?Upload
    {
        return $this->image;
    }
}
