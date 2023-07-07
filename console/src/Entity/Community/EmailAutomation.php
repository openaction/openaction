<?php

namespace App\Entity\Community;

use App\Entity\Area;
use App\Entity\Organization;
use App\Entity\Util;
use App\Entity\Website\Form;
use App\Form\Community\Model\EmailAutomationMetaData;
use App\Repository\Community\EmailAutomationRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EmailAutomationRepository::class)]
#[ORM\Table('community_email_automations')]
class EmailAutomation
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityOrganizationTrait;
    use Util\EntityTimestampableTrait;

    public const TRIGGER_NEW_CONTACT = 'new_contact';
    public const TRIGGER_NEW_FORM_ANSWER = 'new_form_answer';
    public const TYPE_CONTACT = 'contact';
    public const TYPE_MEMBER = 'member';

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 50)]
    private string $trigger;

    #[ORM\Column(length: 250)]
    private string $fromEmail;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $replyToEmail;

    #[ORM\Column(length: 150)]
    private string $subject;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $toEmail = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $fromName = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $replyToName;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $preview = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: 'boolean')]
    private bool $unlayerEnabled = true;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $unlayerDesign = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $typeFilter = null;

    #[ORM\ManyToOne(targetEntity: Area::class)]
    private ?Area $areaFilter = null;

    #[ORM\ManyToOne(targetEntity: Tag::class)]
    private ?Tag $tagFilter = null;

    #[ORM\ManyToOne(targetEntity: Form::class)]
    private ?Form $formFilter = null;

    #[ORM\Column(type: 'integer')]
    private int $weight = 1;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = false;

    #[ORM\OneToMany(targetEntity: EmailAutomationMessage::class, mappedBy: 'automation', cascade: ['remove'])]
    private Collection $messages;

    public function __construct(
        Organization $organization,
        string $name,
        string $trigger,
        string $fromEmail,
        string $subject,
        string $replyToEmail = null,
        string $replyToName = null
    ) {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->organization = $organization;
        $this->name = $name;
        $this->trigger = $trigger;
        $this->fromEmail = $fromEmail;
        $this->replyToEmail = $replyToEmail;
        $this->replyToName = $replyToName;
        $this->subject = $subject;
        $this->messages = new ArrayCollection();
    }

    public static function createDefault(
        Organization $organization,
        string $name,
        string $fromEmail,
        string $fromName,
        string $subject,
        int $weight = 1
    ): self {
        $self = new self($organization, $name, self::TRIGGER_NEW_CONTACT, $fromEmail, $subject);
        $self->fromName = $fromName;
        $self->weight = $weight;

        return $self;
    }

    public static function createFixture(array $data): self
    {
        $self = new EmailAutomation(
            $data['orga'],
            $data['name'],
            $data['trigger'] ?? self::TRIGGER_NEW_CONTACT,
            $data['fromEmail'] ?? 'noreply@citipo.com',
            $data['subject'],
            $data['replyToEmail'] ?? null,
            $data['replyToName'] ?? null
        );

        $self->toEmail = $data['toEmail'] ?? null;
        $self->fromName = $data['fromName'] ?? 'Citipo';
        $self->replyToEmail = $data['replyToEmail'] ?? 'citipo@citipo.com';
        $self->replyToName = $data['replyToName'] ?? 'Citipo';
        $self->content = $data['content'] ?? '';
        $self->unlayerDesign = $data['unlayerDesign'] ?? null;
        $self->unlayerEnabled = $data['unlayerEnabled'] ?? true;
        $self->typeFilter = $data['typeFilter'] ?? null;
        $self->areaFilter = $data['areaFilter'] ?? null;
        $self->tagFilter = $data['tagFilter'] ?? null;
        $self->formFilter = $data['formFilter'] ?? null;
        $self->enabled = $data['enabled'] ?? true;
        $self->createdAt = $data['createdAt'] ?? new \DateTime();
        $self->weight = $data['weight'] ?? 1;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function applyMetadata(EmailAutomationMetaData $metadata)
    {
        $this->name = (string) $metadata->name;
        $this->trigger = (string) $metadata->trigger;
        $this->subject = (string) $metadata->subject;
        $this->preview = $metadata->preview ?: null;
        $this->fromEmail = (string) $metadata->fromEmail;
        $this->fromName = $metadata->fromName ?: null;
        $this->replyToEmail = $metadata->replyToEmail ?: null;
        $this->replyToName = $metadata->replyToName ?: null;
        $this->toEmail = 'specific' === $metadata->toEmailType && $metadata->toEmail ? $metadata->toEmail : null;
        $this->typeFilter = $metadata->typeFilter ?: null;
        $this->formFilter = $metadata->formFilter;
    }

    public function applyUnlayerUpdate(array $design, ?string $content)
    {
        $this->unlayerDesign = $design;
        $this->unlayerEnabled = true;
        $this->content = $content ?: '';
    }

    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTrigger(): string
    {
        return $this->trigger;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function getReplyToEmail(): ?string
    {
        return $this->replyToEmail;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getToEmail(): ?string
    {
        return $this->toEmail;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function getReplyToName(): ?string
    {
        return $this->replyToName;
    }

    public function getPreview(): ?string
    {
        return $this->preview;
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

    public function getTypeFilter(): ?string
    {
        return $this->typeFilter;
    }

    public function getAreaFilter(): ?Area
    {
        return $this->areaFilter;
    }

    public function getTagFilter(): ?Tag
    {
        return $this->tagFilter;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return Collection|EmailAutomationMessage[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function getFormFilter(): ?Form
    {
        return $this->formFilter;
    }
}
