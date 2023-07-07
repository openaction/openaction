<?php

namespace App\Form\Integration\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TelegramAppData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $botUsername;
}
