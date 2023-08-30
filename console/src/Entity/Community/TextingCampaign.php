<?php

namespace App\Entity\Community;

use App\Entity\Area;
use App\Entity\Project;
use App\Entity\Util;
use App\Form\Community\Model\TextingCampaignMetaData;
use App\Repository\Community\TextingCampaignRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TextingCampaignRepository::class)]
#[ORM\Table('community_texting_campaigns')]
class TextingCampaign implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;

    use Util\EntityMemberRestrictedTrait;
    public const FILTER_OR = 'or';
    public const FILTER_AND = 'and';

    #[ORM\Column(length: 500)]
    private ?string $content;

    #[ORM\ManyToMany(targetEntity: Area::class)]
    #[ORM\JoinTable(name: 'community_texting_campaigns_areas_filter')]
    private Collection $areasFilter;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: 'community_texting_campaigns_tags_filter')]
    private Collection $tagsFilter;

    #[ORM\Column(length: 10)]
    private string $tagsFilterType = self::FILTER_OR;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $contactsFilter = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $sentAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $resolvedAt;

    #[ORM\OneToMany(targetEntity: TextingCampaignMessage::class, mappedBy: 'campaign', cascade: ['remove'])]
    private Collection $messages;

    public function __construct(Project $project, string $content)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->content = $content;
        $this->messages = new ArrayCollection();
        $this->areasFilter = new ArrayCollection();
        $this->tagsFilter = new ArrayCollection();
    }

    /*
     * Factories
     */
    public static function createFixture(array $data): self
    {
        $self = new TextingCampaign($data['project'], $data['content']);
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->sentAt = $data['sentAt'] ?? null;
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;
        $self->resolvedAt = $data['resolvedAt'] ?? null;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function duplicate(): self
    {
        $campaign = new self($this->project, $this->content);
        $campaign->contactsFilter = $this->contactsFilter;

        foreach ($this->areasFilter as $area) {
            $campaign->areasFilter->add($area);
        }

        foreach ($this->tagsFilter as $tag) {
            $campaign->tagsFilter->add($tag);
        }

        return $campaign;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'texting-campaign';
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
        return $this->content;
    }

    public function getSearchContent(): ?string
    {
        return strip_tags($this->content);
    }

    public function getSearchCategoriesFacet(): array
    {
        return [];
    }

    public function getSearchStatusFacet(): ?string
    {
        if ($this->getSentAt()) {
            return 'sent';
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
            'sentAt' => $this->sentAt?->format('Y-m-d H:i:s'),
        ];
    }

    /*
     * Setters
     */
    public function applyMetadataUpdate(TextingCampaignMetaData $metadata): void
    {
        $this->content = (string) $metadata->content;
        $this->tagsFilterType = (string) $metadata->tagsFilterType;
        $this->onlyForMembers = $metadata->isOnlyForMembers();
    }

    public function applyContentUpdate(string $content): void
    {
        $this->content = $content;
    }

    public function applyContactsFilterUpdate(array $contactsFilter): void
    {
        $this->contactsFilter = $contactsFilter ?: null;
    }

    public function markSent(): void
    {
        $this->sentAt = new \DateTime();
    }

    public function markResolved()
    {
        $this->resolvedAt = new \DateTime();
    }

    /*
     * Getters
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getContactsFilter(): ?array
    {
        return $this->contactsFilter;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    public function getResolvedAt(): ?\DateTime
    {
        return $this->resolvedAt;
    }

    /**
     * @return Collection|TextingCampaignMessage[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
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
