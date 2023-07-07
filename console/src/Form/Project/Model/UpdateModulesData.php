<?php

namespace App\Form\Project\Model;

use App\Entity\Project;
use App\Platform\Features;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateModulesData
{
    #[Assert\All([new Assert\Choice(['callback' => [Features::class, 'allModules']])])]
    #[Assert\NotBlank(message: 'console.organization.manage_project.missing_module')]
    public array $modules;

    #[Assert\All([new Assert\Choice(['callback' => [Features::class, 'allTools']])])]
    #[Assert\NotBlank(message: 'console.organization.manage_project.missing_tool')]
    public array $tools = [];

    public static function createFromProject(Project $project): self
    {
        $self = new self();
        $self->modules = $project->getModules();
        $self->tools = $project->getTools();

        return $self;
    }
}
