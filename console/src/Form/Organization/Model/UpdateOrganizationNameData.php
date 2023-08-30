<?php

namespace App\Form\Organization\Model;

use App\Entity\Organization;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateOrganizationNameData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    public ?string $name = null;

    public static function createFromOrganization(Organization $organization): self
    {
        $self = new self();
        $self->name = $organization->getName();

        return $self;
    }
}
