<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use Symfony\Component\Validator\Constraints as Assert;

class LoginApiData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 250)]
    public $email;

    #[Assert\NotBlank]
    public $password;

    public static function createFromPayload(array $data): self
    {
        $self = new self();
        $self->email = $data['email'] ?? null;
        $self->password = $data['password'] ?? null;

        return $self;
    }
}
