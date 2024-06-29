<?php

namespace App\Form\Website\Model;

use Symfony\Component\Validator\Constraints as Assert;

class LaunchPetitionData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $title = null;

    #[Assert\NotBlank]
    public ?string $content = null;

    #[Assert\NotBlank]
    public ?string $mainImage = null;
}
