<?php

namespace App\Form\Project\Model;

use App\Entity\Organization;
use Symfony\Component\Validator\Constraints as Assert;

class MoveProjectData
{
    #[Assert\NotBlank]
    public ?Organization $into = null;
}
