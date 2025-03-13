<?php

namespace App\Entity\Community;

use App\Community\ContactViewBuilder;
use App\Entity\Area;
use App\Entity\Project;
use App\Entity\Util;
use App\Form\Community\Model\EmailingCampaignMetaData;
use App\Repository\Community\EmailingCampaignRepository;
use App\Search\Model\Searchable;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EmailingCampaignRepository::class)]
#[ORM\Table('community_emailing_campaigns')]
class EmailingCampaign implements Searchable
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;
    use Util\EntityMemberRestrictedTrait;

    public const FILTER_OR = 'or';
    public const FILTER_AND = 'and';

    #[ORM\Column(length: 150)]
    private string $subject;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $preview = null;

    #[ORM\Column(length: 250)]
    private string $fromEmail;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $fromName;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $replyToEmail;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $replyToName;

    #[ORM\Column(type: 'boolean')]
    private bool $trackOpens = true;

    #[ORM\Column(type: 'boolean')]
    private bool $trackClicks = true;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content;

    #[ORM\Column(type: 'boolean')]
    private bool $unlayerEnabled = true;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $unlayerDesign = null;

    #[ORM\ManyToMany(targetEntity: Area::class)]
    #[ORM\JoinTable(name: 'community_emailing_campaigns_areas_filter')]
    private Collection $areasFilter;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: 'community_emailing_campaigns_tags_filter')]
    private Collection $tagsFilter;

    #[ORM\Column(length: 10)]
    private string $tagsFilterType = ContactViewBuilder::FILTER_OR;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $contactsFilter = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $sentAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $resolvedAt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\OneToMany(targetEntity: EmailingCampaignMessage::class, mappedBy: 'campaign', cascade: ['remove'])]
    private Collection $messages;

    public function __construct(Project $project, string $subject, string $fromEmail, string $fromName = null, $replyToEmail = null, $replyToName = null)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->subject = $subject;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->replyToEmail = $replyToEmail;
        $this->replyToName = $replyToName;
        $this->messages = new ArrayCollection();
        $this->areasFilter = new ArrayCollection();
        $this->tagsFilter = new ArrayCollection();
    }

    /*
     * Factories
     */
    public static function createFixture(array $data): self
    {
        $self = new EmailingCampaign($data['project'], $data['subject'], $data['fromEmail'] ?? 'noreply', $data['fromName'] ?? 'Citipo', $data['replyToEmail'] ?? null, $data['replyToName'] ?? null);
        $self->preview = $data['preview'] ?? null;
        $self->trackOpens = $data['trackOpens'] ?? true;
        $self->trackClicks = $data['trackClicks'] ?? true;
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->sentAt = $data['sentAt'] ?? null;
        $self->resolvedAt = $data['resolvedAt'] ?? null;
        $self->content = $data['content'] ?? '';
        $self->unlayerDesign = $data['unlayerDesign'] ?? null;
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function duplicate(): self
    {
        $self = new self($this->project, $this->subject, $this->fromEmail, $this->fromName, $this->replyToEmail, $this->replyToName);
        $self->preview = $this->preview;
        $self->content = $this->content;
        $self->unlayerDesign = $this->unlayerDesign;
        $self->unlayerEnabled = $this->unlayerEnabled;
        $self->trackOpens = $this->trackOpens;
        $self->trackClicks = $this->trackClicks;
        $self->contactsFilter = $this->contactsFilter;

        foreach ($this->areasFilter as $area) {
            $self->areasFilter->add($area);
        }

        foreach ($this->tagsFilter as $tag) {
            $self->tagsFilter->add($tag);
        }

        return $self;
    }

    /*
     * Searchable
     */
    public function getSearchType(): string
    {
        return 'emailing-campaign';
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
        return $this->subject;
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
            'preview' => $this->preview,
            'fromEmail' => $this->fromEmail,
            'fromName' => $this->fromName,
            'replyToEmail' => $this->replyToEmail,
            'replyToName' => $this->replyToName,
            'sentAt' => $this->sentAt?->format('Y-m-d H:i:s'),
        ];
    }

    /*
     * Setters
     */
    public function applyMetadataUpdate(EmailingCampaignMetaData $metadata)
    {
        $this->subject = (string) $metadata->subject;
        $this->preview = $metadata->preview;
        $this->fromEmail = (string) $metadata->fromEmail;
        $this->fromName = $metadata->fromName;
        $this->replyToEmail = (string) $metadata->replyToEmail;
        $this->replyToName = $metadata->replyToName;
        $this->tagsFilterType = (string) $metadata->tagsFilterType;
        $this->onlyForMembers = $metadata->isOnlyForMembers();
    }

    public function applyContentUpdate(?string $content)
    {
        $this->content = $content ?: '';
    }

    public function applyUnlayerUpdate(array $design, ?string $content)
    {
        $this->unlayerDesign = $design;
        $this->unlayerEnabled = true;
        $this->content = $content ?: '';
    }

    public function applyContactsFilterUpdate(array $contactsFilter)
    {
        $this->contactsFilter = $contactsFilter ?: null;
    }

    public function markSentExternally(string $externalId)
    {
        $this->externalId = $externalId;
        $this->resolvedAt = new \DateTime();
        $this->sentAt = new \DateTime();
    }

    public function markSent()
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
    public function getFullFromEmail(): string
    {
        return $this->fromEmail.'@'.$this->project->getEmailingDomain()->getName();
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getPreview(): ?string
    {
        return $this->preview;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function getReplyToEmail(): ?string
    {
        return $this->replyToEmail;
    }

    public function getReplyToName(): ?string
    {
        return $this->replyToName;
    }

    public function hasTrackOpens(): bool
    {
        return $this->trackOpens;
    }

    public function hasTrackClicks(): bool
    {
        return $this->trackClicks;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function isUnlayerEnabled(): bool
    {
        return $this->unlayerEnabled;
    }

    public function getUnlayerDesign(): ?array
    {
        return $this->unlayerDesign;
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
     * @return Collection|EmailingCampaignMessage[]
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
