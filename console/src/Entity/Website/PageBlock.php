<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Util;
use App\Repository\Website\PageBlockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageBlockRepository::class)]
#[ORM\Table('website_page_blocks')]
class PageBlock
{
    use Util\EntityIdTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 30)]
    private string $page;

    #[ORM\Column(length: 30)]
    private string $type;

    #[ORM\Column(type: 'integer')]
    private int $weight;

    #[ORM\Column(type: 'json')]
    private array $config;

    public function __construct(Project $project, string $page, string $type, int $weight = 0, array $config = [])
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->page = $page;
        $this->type = $type;
        $this->weight = $weight;
        $this->config = $config;
    }

    public static function createFixture(array $data): self
    {
        return new self($data['project'], $data['page'], $data['type'], $data['weight'] ?? 1, $data['config'] ?? []);
    }

    public function duplicate(Project $newProject): self
    {
        return new self($newProject, $this->page, $this->type, $this->weight, $this->config);
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setWeight(int $weight)
    {
        $this->weight = $weight;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
