<?php

namespace App\Form\Admin\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class StartOnPremiseData
{
    #[Assert\NotBlank]
    public ?string $circonscription = null;

    #[Assert\NotBlank]
    public ?string $candidateName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public ?string $websiteName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public ?string $websiteDescription = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    public ?string $domain = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public ?string $adminEmail = '';

    public ?string $facebook = '';
    public ?string $twitter = '';
    public ?string $instagram = '';
    public ?string $linkedIn = '';
    public ?string $youtube = '';
    public ?string $medium = '';
    public ?string $telegram = '';
    public ?string $snapchat = '';

    #[Assert\Image(maxSize: '10M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $mainImage = null;

    #[Assert\Image(maxSize: '10M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $favicon = null;

    #[Assert\Image(maxSize: '10M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $shareImage = null;
}
