<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\EmailingCampaignMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailingCampaignMessageRepository::class)]
#[ORM\Table('community_emailing_campaigns_messages_logs')]
class EmailingCampaignMessageLog
{
    use Util\EntityIdTrait;

    public const TYPE_OPEN = 'open';
    public const TYPE_CLICK = 'click';
    public const TYPE_UNSUBSCRIBE = 'unsubscribe';

    #[ORM\ManyToOne(targetEntity: EmailingCampaignMessage::class, inversedBy: 'logs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private EmailingCampaignMessage $message;

    #[ORM\Column(length: 20)]
    private string $type;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $date;

    public function __construct(EmailingCampaignMessage $message, string $type, ?\DateTime $date = null)
    {
        $this->message = $message;
        $this->type = $type;
        $this->date = $date ?: new \DateTime();
    }

    public function getMessage(): EmailingCampaignMessage
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }
}
