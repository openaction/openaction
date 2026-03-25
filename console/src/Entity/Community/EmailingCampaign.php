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
    public const BREVO_SEND_STATE_DRAFT = 'draft';
    public const BREVO_SEND_STATE_QUEUED = 'queued';
    public const BREVO_SEND_STATE_SENDING = 'sending';
    public const BREVO_SEND_STATE_SENT = 'sent';
    public const BREVO_SEND_STATE_FAILED = 'failed';

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

    #[ORM\Column(length: 20, options: ['default' => self::BREVO_SEND_STATE_DRAFT])]
    private string $brevoSendState = self::BREVO_SEND_STATE_DRAFT;

    #[ORM\Column(length: 36, nullable: true)]
    private ?string $sendToken = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $brevoDedupKey = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $brevoRemoteCreatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $brevoRemoteSentAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $externalListId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $globalStatsSent = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $globalStatsOpened = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $globalStatsClicked = null;

    #[ORM\OneToMany(targetEntity: EmailingCampaignMessage::class, mappedBy: 'campaign', cascade: ['remove'])]
    private Collection $messages;

    public function __construct(Project $project, string $subject, string $fromEmail, ?string $fromName = null, $replyToEmail = null, $replyToName = null)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->subject = $subject;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->replyToEmail = $replyToEmail;
        $this->replyToName = $replyToName;
        $this->trackOpens = $project->getOrganization()->getEmailEnableOpenTracking();
        $this->trackClicks = $project->getOrganization()->getEmailEnableClickTracking();
        $this->messages = new ArrayCollection();
        $this->areasFilter = new ArrayCollection();
        $this->tagsFilter = new ArrayCollection();
    }

    /*
     * Factories
     */
    public static function createFixture(array $data): self
    {
        $self = new EmailingCampaign(
            $data['project'],
            $data['subject'] ?? '',
            $data['fromEmail'] ?? 'noreply',
            $data['fromName'] ?? 'Citipo',
            $data['replyToEmail'] ?? null,
            $data['replyToName'] ?? null
        );
        $self->preview = $data['preview'] ?? null;
        $self->trackOpens = $data['trackOpens'] ?? true;
        $self->trackClicks = $data['trackClicks'] ?? true;
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->sentAt = $data['sentAt'] ?? null;
        $self->resolvedAt = $data['resolvedAt'] ?? null;
        $self->brevoSendState = $data['brevoSendState'] ?? self::BREVO_SEND_STATE_DRAFT;
        $self->sendToken = $data['sendToken'] ?? null;
        $self->brevoDedupKey = $data['brevoDedupKey'] ?? null;
        $self->brevoRemoteCreatedAt = $data['brevoRemoteCreatedAt'] ?? null;
        $self->brevoRemoteSentAt = $data['brevoRemoteSentAt'] ?? null;
        $self->content = $data['content'] ?? '';
        $self->unlayerDesign = $data['unlayerDesign'] ?? null;
        $self->onlyForMembers = $data['onlyForMembers'] ?? false;
        $self->globalStatsSent = $data['globalStatsSent'] ?? null;
        $self->globalStatsOpened = $data['globalStatsOpened'] ?? null;
        $self->globalStatsClicked = $data['globalStatsClicked'] ?? null;
        $self->externalListId = $data['externalListId'] ?? $data['brevoListId'] ?? null;

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
        $this->setExternalId($externalId);
        $this->resolvedAt = new \DateTime();
        $this->sentAt = new \DateTime();
    }

    public function setExternalListId(?int $externalListId): void
    {
        $this->externalListId = $externalListId;
    }

    public function setExternalId(?string $externalId): void
    {
        $externalId = null !== $externalId ? trim($externalId) : null;
        $this->externalId = '' === $externalId ? null : $externalId;
    }

    public function markSent()
    {
        $this->sentAt = new \DateTime();
    }

    public function markResolved()
    {
        $this->resolvedAt = new \DateTime();
    }

    public function markBrevoSendQueued(string $sendToken, ?string $brevoDedupKey = null): void
    {
        $this->brevoSendState = self::BREVO_SEND_STATE_QUEUED;
        $this->sendToken = trim($sendToken);

        if ($brevoDedupKey) {
            $this->setBrevoDedupKey($brevoDedupKey);
        }
    }

    public function markBrevoSendSending(): void
    {
        $this->brevoSendState = self::BREVO_SEND_STATE_SENDING;
    }

    public function markBrevoRemoteCreated(string $externalId): void
    {
        $this->setExternalId($externalId);

        if (null === $this->brevoRemoteCreatedAt) {
            $this->brevoRemoteCreatedAt = new \DateTime();
        }
    }

    public function markBrevoRemoteSent(): void
    {
        if (null === $this->brevoRemoteSentAt) {
            $this->brevoRemoteSentAt = new \DateTime();
        }
    }

    public function markBrevoSendSent(string $externalId): void
    {
        $this->markBrevoRemoteCreated($externalId);
        $this->markBrevoRemoteSent();
        $this->brevoSendState = self::BREVO_SEND_STATE_SENT;
        $this->resolvedAt = new \DateTime();
        $this->sentAt = new \DateTime();
    }

    public function markBrevoSendFailed(): void
    {
        $this->brevoSendState = self::BREVO_SEND_STATE_FAILED;
    }

    public function resetBrevoSendToDraft(): void
    {
        $this->brevoSendState = self::BREVO_SEND_STATE_DRAFT;
        $this->sendToken = null;
    }

    public function updateGlobalStats(?int $sent, ?int $opened, ?int $clicked): void
    {
        $this->globalStatsSent = $sent;
        $this->globalStatsOpened = $opened;
        $this->globalStatsClicked = $clicked;
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

    public function isMutableDraftState(): bool
    {
        return null === $this->sentAt
            && self::BREVO_SEND_STATE_DRAFT === $this->brevoSendState;
    }

    public function getBrevoSendState(): string
    {
        return $this->brevoSendState;
    }

    public function isBrevoSendDraftState(): bool
    {
        return self::BREVO_SEND_STATE_DRAFT === $this->brevoSendState;
    }

    public function isBrevoSendQueuedState(): bool
    {
        return self::BREVO_SEND_STATE_QUEUED === $this->brevoSendState;
    }

    public function isBrevoSendSendingState(): bool
    {
        return self::BREVO_SEND_STATE_SENDING === $this->brevoSendState;
    }

    public function isBrevoSendSentState(): bool
    {
        return self::BREVO_SEND_STATE_SENT === $this->brevoSendState;
    }

    public function isBrevoSendFailedState(): bool
    {
        return self::BREVO_SEND_STATE_FAILED === $this->brevoSendState;
    }

    public function getSendToken(): ?string
    {
        return $this->sendToken;
    }

    public function hasMatchingSendToken(?string $sendToken): bool
    {
        if (null === $sendToken) {
            return false;
        }

        return $this->sendToken === trim($sendToken);
    }

    public function getBrevoDedupKey(): ?string
    {
        return $this->brevoDedupKey;
    }

    public function setBrevoDedupKey(?string $brevoDedupKey): void
    {
        $brevoDedupKey = null !== $brevoDedupKey ? trim($brevoDedupKey) : null;
        $this->brevoDedupKey = '' === $brevoDedupKey ? null : $brevoDedupKey;
    }

    public function getBrevoRemoteCreatedAt(): ?\DateTime
    {
        return $this->brevoRemoteCreatedAt;
    }

    public function getBrevoRemoteSentAt(): ?\DateTime
    {
        return $this->brevoRemoteSentAt;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function getExternalListId(): ?int
    {
        return $this->externalListId;
    }

    public function getGlobalStatsSent(): ?int
    {
        return $this->globalStatsSent;
    }

    public function getGlobalStatsOpened(): ?int
    {
        return $this->globalStatsOpened;
    }

    public function getGlobalStatsClicked(): ?int
    {
        return $this->globalStatsClicked;
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
