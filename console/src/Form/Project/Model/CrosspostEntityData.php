<?php

namespace App\Form\Project\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CrosspostEntityData
{
    #[Assert\NotBlank]
    public ?iterable $intoProjects = null;
}
