<?php

namespace App\Form\Website\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class LocalizedPetitionImageData
{
    #[Assert\NotNull]
    #[Assert\Image]
    public ?UploadedFile $file = null;
}
