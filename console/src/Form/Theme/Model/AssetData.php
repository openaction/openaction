<?php

namespace App\Form\Theme\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class AssetData
{
    #[Assert\NotBlank]
    #[Assert\File(maxSize: '10Mi', mimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'text/css', 'application/json', 'application/javascript', 'video/mp4', 'font/ttf', 'font/woff', 'font/woff2'])]
    public ?UploadedFile $file = null;
}
