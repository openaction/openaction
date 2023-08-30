<?php

namespace App\Form\Project\Model;

use App\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;

class MoveEntityData
{
    #[Assert\NotBlank]
    public ?Project $into = null;
}
