<?php

namespace App\Entity\Community;

use App\Entity\Util;
use App\Repository\Community\EmailBatchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailBatchRepository::class)]
#[ORM\Table('community_email_batches')]
class EmailBatch
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 100)]
    private string $source;

    #[ORM\Column(length: 30)]
    private string $emailProvider;

    #[ORM\Column(type: 'json', options: ['jsonb' => true])]
    private array $payload;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $sentAt = null;

    public function __construct(string $source, string $emailProvider, array $payload)
    {
        $this->source = $source;
        $this->emailProvider = $emailProvider;
        $this->payload = $payload;
        $this->populateTimestampable();
    }

    public function getSource(): string
    {
        return $this->source;
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
