<?php

namespace App\Entity\Util;

use App\Entity\Project;
use Doctrine\ORM\Mapping as ORM;

trait EntityProjectTrait
{
    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Project $project = null;

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    public function getSearchAccessibleFromProjects(): array
    {
        return [$this->project->getUuid()->toRfc4122()];
    }

    public function getSearchOrganization(): string
    {
        return $this->project->getOrganization()->getUuid()->toRfc4122();
    }
}
