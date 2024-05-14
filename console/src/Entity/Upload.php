<?php

namespace App\Entity;

use App\Repository\UploadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UploadRepository::class)]
#[ORM\Table('uploads')]
class Upload
{
    use Util\EntityIdTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Project $project;

    #[ORM\Column(type: 'text', unique: true)]
    private string $pathname;

    public function __construct(string $pathname, ?Project $project)
    {
        $this->populateTimestampable();
        $this->project = $project;
        $this->pathname = $pathname;
    }

    public static function createFixture(array $data): self
    {
        return new self($data['name'], $data['project'] ?? null);
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getExtension(): string
    {
        return pathinfo($this->pathname, PATHINFO_EXTENSION);
    }
}
