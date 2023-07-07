<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Util;
use App\Form\Developer\Model\RedirectionData;
use App\Repository\Website\RedirectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RedirectionRepository::class)]
#[ORM\Table('website_redirections')]
class Redirection implements \Stringable
{
    use Util\EntityIdTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 250)]
    private string $source;

    #[ORM\Column(length: 250)]
    private string $target;

    #[ORM\Column(type: 'integer')]
    private int $code;

    #[ORM\Column(type: 'integer')]
    private int $weight;

    public function __construct(Project $project, string $source, string $target, int $code = 302, int $weight = 1)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->source = $source;
        $this->target = $target;
        $this->code = $code;
        $this->weight = $weight;
    }

    public function __toString(): string
    {
        return $this->source.' => '.$this->target;
    }

    public static function createFixture(array $data): self
    {
        return new self($data['project'], $data['source'], $data['target'], $data['code'] ?? 302, $data['weight'] ?? 1);
    }

    public function applyDataUpdate(RedirectionData $data)
    {
        $this->source = $data->source;
        $this->target = $data->target;
        $this->code = $data->code;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}
