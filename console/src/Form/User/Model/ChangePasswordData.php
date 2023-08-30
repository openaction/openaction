<?php

namespace App\Form\User\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordData
{
    #[SecurityAssert\UserPassword]
    #[Assert\NotBlank]
    public ?string $oldPassword = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public ?string $newPassword = null;
}
