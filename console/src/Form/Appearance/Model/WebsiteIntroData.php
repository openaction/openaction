<?php

namespace App\Form\Appearance\Model;

use App\Entity\Project;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class WebsiteIntroData
{
    #[Assert\Length(max: 80)]
    public ?string $websiteMainIntroTitle = null;

    public ?string $websiteMainIntroContent = null;

    #[Assert\Image(maxSize: '10M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $websiteMainImage = null;

    #[Assert\File(maxSize: '5M', mimeTypes: ['video/mp4'])]
    public ?UploadedFile $websiteMainVideo = null;

    public function __construct(?string $websiteMainIntroTitle = null, ?string $websiteMainIntroContent = null)
    {
        $this->websiteMainIntroTitle = $websiteMainIntroTitle;
        $this->websiteMainIntroContent = $websiteMainIntroContent;
    }

    public static function createFromProject(Project $project): self
    {
        return new self($project->getWebsiteMainIntroTitle(), $project->getWebsiteMainIntroContent());
    }
}
