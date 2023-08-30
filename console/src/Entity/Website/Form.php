<?php

namespace App\Entity\Website;

use App\Entity\Community\PhoningCampaign;
use App\Entity\Project;
use App\Entity\Util;
use App\Form\Website\Model\FormData;
use App\Repository\Website\FormRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FormRepository::class)]
#[ORM\Table('website_forms')]
class Form implements Searchable
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
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    private int $weight;

    #[ORM\Column(type: 'boolean')]
    private bool $proposeNewsletter = true;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $redirectUrl = null;

    #[ORM\OneToOne(targetEntity: PhoningCampaign::class, mappedBy: 'form', cascade: ['persist', 'remove'])]
    private ?PhoningCampaign $phoningCampaign = null;

    #[ORM\OneToMany(targetEntity: FormBlock::class, mappedBy: 'form', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['weight' => 'ASC'])]
    private ?Collection $blocks;

    #[ORM\OneToMany(targetEntity: FormAnswer::class, mappedBy: 'form')]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private ?Collection $answers;

    public function __construct(Project $project, string $title, int $weight = 1)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->title = $title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
        $this->weight = $weight;
        $this->blocks = new ArrayCollection();
        $this->answers = new ArrayCollection();
    }

    /*
     * Factories
     */
    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['title'], $data['weight'] ?? 1);
        $self->description = $data['description'] ?? null;
        $self->proposeNewsletter = $data['proposeNewsletter'] ?? true;
        $self->onlyForMembers = $data['onlyForMembers'] ?? true;
        $self->redirectUrl = $data['redirectUrl'] ?? null;
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->pageViews = $data['pageViews'] ?? 0;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function duplicate(): self
    {
        $self = new self($this->project, $this->title, $this->weight);
        $self->description = $this->description;
        $self->proposeNewsletter = $this->proposeNewsletter;
        $self->onlyForMembers = $this->onlyForMembers;

        /** @var FormBlock $block */
        foreach ($this->blocks as $block) {
            $self->getBlocks()->add($block->duplicate($self));
        }

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'form';
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
        return $this->description;
    }

    public function getSearchCategoriesFacet(): array
    {
        return [];
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
        ];
    }

    /*
     * Setters
     */
    public function applyUpdate(FormData $data)
    {
        $this->title = (string) $data->title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
        $this->description = $data->description;
        $this->proposeNewsletter = $data->proposeNewsletter;
        $this->onlyForMembers = $data->onlyForMembers;
        $this->redirectUrl = $data->redirectUrl;
    }

    public function hasEmailBlock(): bool
    {
        foreach ($this->getBlocks() as $block) {
            if (FormBlock::TYPE_EMAIL === $block->getType()) {
                return true;
            }
        }

        return false;
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

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function proposeNewsletter(): bool
    {
        return $this->proposeNewsletter;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getPhoningCampaign(): ?PhoningCampaign
    {
        return $this->phoningCampaign;
    }

    /**
     * @return Collection|FormBlock[]
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    /**
     * @return Collection|FormAnswer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }
}
