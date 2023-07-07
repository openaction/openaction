<?php

namespace App\Form\Member\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateEmailData
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
