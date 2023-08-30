<?php

// PHPStorm inspection disabled as we want to check for type through the validator
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Api\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ContactPictureApiData
{
    #[Assert\NotBlank]
    #[Assert\Image(maxSize: '5M')]
    public $picture;

    public function __construct($picture)
    {
        $this->picture = $picture;
    }
}
