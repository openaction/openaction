<?php

namespace App\Form\User\Model;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFinalizingData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public ?string $password = null;
}
