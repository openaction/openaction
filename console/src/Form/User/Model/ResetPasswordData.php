<?php

namespace App\Form\User\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public ?string $newPassword = null;
}
