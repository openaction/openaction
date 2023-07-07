<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\TextingCampaignMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TextingCampaignMessageRepository::class)]
#[ORM\Table('community_texting_campaigns_messages')]
class TextingCampaignMessage
{
    use Util\EntityIdTrait;

    #[ORM\ManyToOne(targetEntity: TextingCampaign::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private TextingCampaign $campaign;

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Contact $contact;

    #[ORM\Column(type: 'boolean')]
    private bool $sent = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $sentAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $delivered = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $deliveredAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $bounced = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $bouncedAt = null;

    public function __construct(TextingCampaign $campaign, Contact $contact)
    {
        $this->campaign = $campaign;
        $this->contact = $contact;
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

        return $self;
    }

    public function markSent(): void
    {
        $this->sent = true;
        $this->sentAt = new \DateTime();
    }

    public function markDelivered(): void
    {
        $this->delivered = true;
        $this->deliveredAt = new \DateTime();
    }

    public function markBounced(): void
    {
        if (!$this->sent) {
            $this->markSent();
        }

        $this->bounced = true;
        $this->bouncedAt = new \DateTime();
    }

    public function getCampaign(): TextingCampaign
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

    public function isDelivered(): bool
    {
        return $this->delivered;
    }

    public function getDeliveredAt(): ?\DateTime
    {
        return $this->deliveredAt;
    }

    public function isBounced(): bool
    {
        return $this->bounced;
    }

    public function getBouncedAt(): ?\DateTime
    {
        return $this->bouncedAt;
    }
}
