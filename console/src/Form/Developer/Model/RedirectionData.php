<?php

namespace App\Form\Developer\Model;

use App\Entity\Website\Redirection;
use Symfony\Component\Validator\Constraints as Assert;

class RedirectionData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    public $source;

    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    public $target;

    #[Assert\NotBlank]
    #[Assert\Choice([301, 302])]
    public $code;

    public static function createFromRedirection(Redirection $item): self
    {
        $self = new self();
        $self->source = $item->getSource();
        $self->target = $item->getTarget();
        $self->code = $item->getCode();

        return $self;
    }
}
