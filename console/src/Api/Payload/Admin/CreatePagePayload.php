<?php

namespace App\Api\Payload\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePagePayload
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public mixed $title = null;

    #[Assert\Type('string')]
    public mixed $content = null;

    #[Assert\Type('string')]
    #[Assert\Length(max: 200)]
    public mixed $description = null;

    #[Assert\Type('array')]
    #[Assert\All([new Assert\Type('string')])]
    public mixed $categories = [];
}
