<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\EmailingCampaignBatchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailingCampaignBatchRepository::class)]
#[ORM\Table('community_emailing_campaigns_batches')]
class EmailingCampaignBatch
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: EmailingCampaign::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private EmailingCampaign $campaign;

    #[ORM\Column(length: 30)]
    private string $emailProvider;

    #[ORM\Column(type: 'json', options: ['jsonb' => true])]
    private array $payload;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $sentAt = null;

    public function __construct(EmailingCampaign $campaign, string $emailProvider, array $payload)
    {
        $this->campaign = $campaign;
        $this->emailProvider = $emailProvider;
        $this->payload = $payload;
        $this->populateTimestampable();
    }

    public function getCampaign(): EmailingCampaign
    {
        return $this->campaign;
    }

    public function getEmailProvider(): string
    {
        return $this->emailProvider;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }
}
