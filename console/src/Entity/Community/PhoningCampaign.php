<?php

namespace App\Entity\Community;

use App\Entity\Area;
use App\Entity\Project;
use App\Entity\Util;
use App\Entity\Website\Form;
use App\Form\Community\Model\PhoningCampaignMetaData;
use App\Repository\Community\PhoningCampaignRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PhoningCampaignRepository::class)]
#[ORM\Table('community_phoning_campaigns')]
class PhoningCampaign implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityMemberRestrictedTrait;

    public const FILTER_OR = 'or';
    public const FILTER_AND = 'and';
    public const DEFAULT_END_AFTER = 24;

    #[ORM\Column(length: 160)]
    private ?string $name;

    #[ORM\OneToOne(targetEntity: Form::class, inversedBy: 'phoningCampaign', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'SET NULL')]
    private Form $form;

    #[ORM\ManyToMany(targetEntity: Area::class)]
    #[ORM\JoinTable(name: 'community_phoning_campaigns_areas_filter')]
    private Collection $areasFilter;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: 'community_phoning_campaigns_tags_filter')]
    private Collection $tagsFilter;

    #[ORM\Column(length: 10)]
    private string $tagsFilterType = self::FILTER_OR;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $contactsFilter = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $startAt;

    #[ORM\Column(type: 'integer')]
    private int $endAfter = PhoningCampaign::DEFAULT_END_AFTER;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $resolvedAt;

    /**
     * @var Collection|PhoningCampaignTarget[]
     */
    #[ORM\OneToMany(targetEntity: PhoningCampaignTarget::class, mappedBy: 'campaign', cascade: ['persist', 'remove'])]
    private Collection $targets;

    public function __construct(Project $project, string $name)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->name = $name;
        $this->form = new Form($project, $name);
        $this->areasFilter = new ArrayCollection();
        $this->tagsFilter = new ArrayCollection();
        $this->targets = new ArrayCollection();
    }

    /*
     * Factories
     */
    public static function createFixture(array $data): self
    {
        $self = new PhoningCampaign($data['project'], $data['name']);
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->startAt = $data['startAt'] ?? null;
        $self->resolvedAt = $data['resolvedAt'] ?? null;
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;
        $self->endAfter = $data['endAfter'] ?? self::DEFAULT_END_AFTER;

        if (isset($data['form']) && $data['form']) {
            $self->form = $data['form'];
        }

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function duplicate(): self
    {
        $campaign = new self($this->project, $this->name);
        $campaign->contactsFilter = $this->contactsFilter;
        $campaign->onlyForMembers = $this->onlyForMembers;
        $campaign->endAfter = $this->endAfter;

        foreach ($this->areasFilter as $area) {
            $campaign->areasFilter->add($area);
        }

        foreach ($this->tagsFilter as $tag) {
            $campaign->tagsFilter->add($tag);
        }

        return $campaign;
    }

    public static function getEndAfterRanges(): array
    {
        return [3, 6, 12, 24, 48, 72, 168, 720];
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'phoning-campaign';
    }

    public function isSearchPublic(): bool
    {
        return false;
    }

    public function getSearchUuid(): string
    {
        return $this->uuid->toRfc4122();
    }

    public function getSearchTitle(): string
    {
        return $this->name;
    }

    public function getSearchContent(): ?string
    {
        return null;
    }

    public function getSearchCategoriesFacet(): array
    {
        return [];
    }

    public function getSearchStatusFacet(): ?string
    {
        if ($this->getStartAt()) {
            return 'started';
        }

        return 'draft';
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
            'startAt' => $this->startAt?->format('Y-m-d H:i:s'),
        ];
    }

    /*
     * Setters
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function applyMetadataUpdate(PhoningCampaignMetaData $metadata): void
    {
        $this->name = (string) $metadata->name;
        $this->endAfter = (int) $metadata->endAfter;
        $this->tagsFilterType = (string) $metadata->tagsFilterType;
        $this->onlyForMembers = $metadata->isOnlyForMembers();
    }

    public function applyContentUpdate(string $content): void
    {
        $this->name = $content;
    }

    public function applyContactsFilterUpdate(array $contactsFilter): void
    {
        $this->contactsFilter = $contactsFilter ?: null;
    }

    public function start()
    {
        $this->startAt = new \DateTime();
        $this->resolvedAt = null;
    }

    public function stop()
    {
        if ($this->startAt) {
            $diff = (new \DateTime())->diff($this->startAt);
            $this->endAfter = $diff->days * 24 + $diff->h;
        }
    }

    public function markResolved()
    {
        $this->resolvedAt = new \DateTime();
    }

    /*
     * Getters
     */
    public function getEndAt(): ?\DateTime
    {
        if (!$this->startAt) {
            return null;
        }

        $endAt = clone $this->startAt;
        $endAt->modify('+'.$this->endAfter.' hours');

        return $endAt;
    }

    public function isActive(): bool
    {
        $now = new \DateTime();

        return $this->startAt
            && $this->startAt < $now
            && $this->getEndAt() > $now;
    }

    public function isFinished(): bool
    {
        return $this->startAt
            && $this->endAfter
            && $this->getEndAt() < new \DateTime();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getContactsFilter(): ?array
    {
        return $this->contactsFilter;
    }

    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    public function getEndAfter(): ?int
    {
        return $this->endAfter;
    }

    public function getResolvedAt(): ?\DateTime
    {
        return $this->resolvedAt;
    }

    /**
     * @return Collection|PhoningCampaignTarget[]
     */
    public function getTargets(): Collection
    {
        return $this->targets;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTagsFilter(): Collection
    {
        return $this->tagsFilter;
    }

    public function getTagsFilterType(): string
    {
        return $this->tagsFilterType;
    }

    public function getTagsFilterIds(): array
    {
        $ids = [];
        foreach ($this->getTagsFilter() as $tag) {
            $ids[] = $tag->getId();
        }

        return $ids;
    }

    public function getTagsFilterNames(): array
    {
        $names = [];
        foreach ($this->getTagsFilter() as $tag) {
            $names[] = $tag->getName();
        }

        return $names;
    }

    /**
     * @return Collection|Area[]
     */
    public function getAreasFilter(): Collection
    {
        return $this->areasFilter;
    }

    public function getAreasFilterIds(): array
    {
        $ids = [];
        foreach ($this->getAreasFilter() as $area) {
            $ids[] = $area->getId();
        }

        return $ids;
    }

    public function getAreasFilterNames(): array
    {
        $names = [];
        foreach ($this->getAreasFilter() as $area) {
            $names[] = $area->getName();
        }

        return $names;
    }
}
