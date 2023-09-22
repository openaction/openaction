<?php

namespace App\Api\Payload\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class CreateRedirectionPayload
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    public mixed $fromUrl = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    public mixed $toUrl = null;

    #[Assert\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Choice([301, 302])]
    public mixed $type = null;

    #[Assert\Type('integer')]
    public mixed $weight = null;
}
