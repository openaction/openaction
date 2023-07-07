<?php

namespace App\Form\Website\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ManifestoTopicImageData
{
    #[Assert\NotBlank]
    #[Assert\Image(maxSize: '20Mi', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $file = null;
}
