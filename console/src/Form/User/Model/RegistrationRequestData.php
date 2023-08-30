<?php

namespace App\Form\User\Model;

use App\Form\User\RegistrationType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationRequestData
{
    #[Assert\Blank]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    public ?string $name = null;

    /**
     * Returns field that matchs with RegistrationType::EMAIL.
     */
    public function getEmail(): ?string
    {
        return $this->{RegistrationType::EMAIL} ?? null;
    }
}
