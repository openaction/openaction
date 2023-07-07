<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use Symfony\Component\Validator\Constraints as Assert;

class FormAnswerApiData
{
    #[Assert\All([new Assert\Type('string')])]
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    public $fields = [];

    public static function createFromPayload(array $data): self
    {
        $self = new self();
        foreach (get_object_vars($self) as $var => $value) {
            $self->{$var} = $data[$var] ?? null;
        }

        return $self;
    }
}
