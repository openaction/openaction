<?php

namespace App\Form\Community\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ContactPictureData
{
    #[Assert\Image(maxSize: '2M', mimeTypes: ['image/jpeg', 'image/png', 'image/gif'])]
    #[Assert\NotBlank]
    public ?UploadedFile $file = null;
} 