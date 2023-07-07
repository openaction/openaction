<?php

namespace App\Form\Appearance\Model;

use App\Entity\Upload;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class LogosData
{
    #[Assert\Image(maxSize: '4M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $appearanceLogoDark = null;

    #[Assert\Image(maxSize: '4M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $appearanceLogoWhite = null;

    #[Assert\Image(maxSize: '2M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $appearanceIcon = null;

    public ?Upload $appearanceLogoDarkUpload = null;
    public ?Upload $appearanceLogoWhiteUpload = null;
    public ?Upload $appearanceIconUpload = null;
}
