<?php

namespace App\Entity\Platform;

use App\Entity\Util;
use App\Repository\Platform\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table('platform_logs')]
class Log
{
    use Util\EntityIdTrait;

    #[ORM\Column(length: '50')]
    private string $type;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'json')]
    private array $payload;

    public function __construct(string $type, array $payload)
    {
        $this->type = $type;
        $this->payload = $payload;
        $this->createdAt = new \DateTime();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
