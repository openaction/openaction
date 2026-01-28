<?php

namespace App\Form\Member\Model;

use Symfony\Component\Validator\Constraints as Assert;

class LoginData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 150)]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public ?string $password = null;
}
