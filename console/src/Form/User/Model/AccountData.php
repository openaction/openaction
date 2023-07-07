<?php

namespace App\Form\User\Model;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class AccountData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Choice(['fr', 'en', 'de'])]
    public ?string $locale = null;

    public static function createFromUser(User $user): self
    {
        $self = new self();
        $self->firstName = $user->getFirstName();
        $self->lastName = $user->getLastName();
        $self->locale = $user->getLocale();

        return $self;
    }
}
