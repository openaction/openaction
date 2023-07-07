<?php

namespace App\Entity\Platform;

use App\Entity\Util;
use App\Repository\Platform\JobRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table('platform_jobs')]
class Job
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: '50')]
    private string $type;

    #[ORM\Column(type: 'bigint')]
    private int $step;

    #[ORM\Column(type: 'bigint')]
    private int $total;

    #[ORM\Column(type: 'json')]
    private array $payload;

    public function __construct(string $type, int $step, int $total)
    {
        $this->populateTimestampable();
        $this->type = $type;
        $this->step = $step;
        $this->total = $total;
        $this->payload = [];
    }

    public function setStep(int $step): void
    {
        $this->step = $step;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function finish(array $payload = []): void
    {
        if (!$this->step) {
            $this->step = 1;
        }

        $this->total = $this->step;
        $this->payload = $payload;
    }

    /**
     * Job progress as percentage.
     */
    public function getProgress(): ?float
    {
        if (!$this->total) {
            return null;
        }

        return round($this->step / $this->total, 2);
    }

    public function isFinished(): bool
    {
        return $this->total > 0 && $this->step === $this->total;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
