<?php

namespace App\Form\Appearance\Model;

use App\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;

class WebsiteAccessData
{
    #[Assert\Length(max: 50)]
    public ?string $websiteAccessUser = null;

    #[Assert\Length(max: 50)]
    public ?string $websiteAccessPass = null;

    public static function createFromProject(Project $project): self
    {
        $self = new self();
        $self->websiteAccessUser = $project->getWebsiteAccessUser();
        $self->websiteAccessPass = $project->getWebsiteAccessPass();

        return $self;
    }
}
