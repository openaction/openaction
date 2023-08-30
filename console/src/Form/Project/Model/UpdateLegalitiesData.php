<?php

namespace App\Form\Project\Model;

use App\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateLegalitiesData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $legalGdprName;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $legalGdprEmail;

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $legalGdprAddress;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $legalPublisherName;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $legalPublisherRole;

    public static function createFromProject(Project $project): self
    {
        $self = new self();
        $self->legalGdprName = $project->getLegalGdprName();
        $self->legalGdprEmail = $project->getLegalGdprEmail();
        $self->legalGdprAddress = $project->getLegalGdprAddress();
        $self->legalPublisherName = $project->getLegalPublisherName();
        $self->legalPublisherRole = $project->getLegalPublisherRole();

        return $self;
    }
}
