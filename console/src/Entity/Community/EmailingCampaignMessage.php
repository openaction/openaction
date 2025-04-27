<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\EmailingCampaignMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailingCampaignMessageRepository::class)]
#[ORM\Table('community_emailing_campaigns_messages', uniqueConstraints: [
    // Prevent campaigns to be sent more than once per contact
    new ORM\UniqueConstraint('community_emailing_campaigns_messages_unique_idx', ['campaign_id', 'contact_id']),
])]
class EmailingCampaignMessage
{
    use Util\EntityIdTrait;

    #[ORM\ManyToOne(targetEntity: EmailingCampaign::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private EmailingCampaign $campaign;

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(type: 'boolean')]
    private bool $sent = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $sentAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $bounced = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $bouncedAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $opened = false;

    #[ORM\Column(type: 'boolean')]
    private bool $clicked = false;

    #[ORM\Column(type: 'boolean')]
    private bool $unsubscribed = false;

    /**
     * @var EmailingCampaignMessageLog[]|Collection
     */
    #[ORM\OneToMany(targetEntity: EmailingCampaignMessageLog::class, mappedBy: 'message', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $logs;

    public function __construct(EmailingCampaign $campaign, Contact $contact)
    {
        $this->campaign = $campaign;
        $this->contact = $contact;
        $this->logs = new ArrayCollection();
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['campaign'], $data['contact']);

        if ($data['sent'] ?? false) {
            $self->markSent();
        }

        if ($data['bounced'] ?? false) {
            $self->markBounced();
        }

        if ($data['opened'] ?? false) {
            for ($i = 0; $i < $data['opened']; ++$i) {
                $self->markOpened();
            }
        }

        if ($data['clicked'] ?? false) {
            for ($i = 0; $i < $data['clicked']; ++$i) {
                $self->markClicked();
            }
        }

        if ($data['unsubscribed'] ?? false) {
            $self->markBounced();
        }

        return $self;
    }

    public function markSent()
    {
        $this->sent = true;
        $this->sentAt = new \DateTime();
    }

    public function markBounced()
    {
        if (!$this->sent) {
            $this->markSent();
        }

        $this->bounced = true;
        $this->bouncedAt = new \DateTime();
    }

    public function markOpened()
    {
        if (!$this->sent) {
            $this->markSent();
        }

        $this->opened = true;
        $this->logs->add(new EmailingCampaignMessageLog($this, EmailingCampaignMessageLog::TYPE_OPEN));
    }

    public function markClicked()
    {
        if (!$this->sent) {
            $this->markSent();
        }

        if (!$this->opened) {
            $this->markOpened();
        }

        $this->clicked = true;
        $this->logs->add(new EmailingCampaignMessageLog($this, EmailingCampaignMessageLog::TYPE_CLICK));
    }

    public function markUnsubscribed()
    {
        if (!$this->sent) {
            $this->markSent();
        }

        $this->unsubscribed = true;
        $this->logs->add(new EmailingCampaignMessageLog($this, EmailingCampaignMessageLog::TYPE_UNSUBSCRIBE));
    }

    public function getOpenedAt(): ?\DateTime
    {
        foreach ($this->logs as $log) {
            if (EmailingCampaignMessageLog::TYPE_OPEN === $log->getType()) {
                return $log->getDate();
            }
        }

        return null;
    }

    public function getClickedAt(): ?\DateTime
    {
        foreach ($this->logs as $log) {
            if (EmailingCampaignMessageLog::TYPE_CLICK === $log->getType()) {
                return $log->getDate();
            }
        }

        return null;
    }

    public function getCampaign(): EmailingCampaign
    {
        return $this->campaign;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    public function isOpened(): bool
    {
        return $this->opened;
    }

    public function isClicked(): bool
    {
        return $this->clicked;
    }

    public function isBounced(): bool
    {
        return $this->bounced;
    }

    public function isUnsubscribed(): bool
    {
        return $this->unsubscribed;
    }

    public function getBouncedAt(): ?\DateTime
    {
        return $this->bouncedAt;
    }

    /**
     * @return Collection|EmailingCampaignMessageLog[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }
}
