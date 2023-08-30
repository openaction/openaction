<?php

namespace App\Form\Admin\Model;

use App\Entity\Organization;
use Symfony\Component\Validator\Constraints as Assert;

class AddDomainData
{
    #[Assert\NotBlank]
    public ?Organization $organization;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 150)]
    public ?string $name = '';
}
