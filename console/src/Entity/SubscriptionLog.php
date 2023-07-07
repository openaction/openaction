<?php

namespace App\Entity;

use App\Repository\SubscriptionLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionLogRepository::class)]
#[ORM\Table('subscriptions_logs')]
class SubscriptionLog
{
    use Util\EntityIdTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'subscriptionLogs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Organization $organization;

    #[ORM\Column(length: 50)]
    private string $message;

    #[ORM\Column(type: 'json')]
    private array $context;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct(Organization $organization, string $message, array $context = [])
    {
        $this->organization = $organization;
        $this->message = $message;
        $this->context = $context;
        $this->createdAt = new \DateTime();
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
