<?php

namespace App\Form\Member\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ResetRequestData
{
    /**
     * @Assert\NotBlank()
     *
     * @Assert\Email()
     *
     * @Assert\Length(max=150)
     */
    public ?string $email = null;
}
