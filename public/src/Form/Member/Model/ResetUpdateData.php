<?php

namespace App\Form\Member\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ResetUpdateData
{
    /**
     * @Assert\NotBlank()
     *
     * @Assert\Length(min=8)
     */
    public ?string $password = null;
}
