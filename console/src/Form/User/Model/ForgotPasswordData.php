<?php

namespace App\Form\User\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;
}
