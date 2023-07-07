<?php

namespace App\Form\Project\Model;

use App\Entity\Project;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateMetasData
{
    #[Assert\Length(max: 60)]
    public ?string $websiteMetaTitle = null;

    #[Assert\Length(max: 255)]
    public ?string $websiteMetaDescription = null;

    #[Assert\Image(maxSize: '10M', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $websiteSharer = null;

    public function __construct(string $metaTitle = null, string $metaDescription = null)
    {
        $this->websiteMetaTitle = $metaTitle;
        $this->websiteMetaDescription = $metaDescription;
    }

    public static function createFromProject(Project $project): self
    {
        return new self($project->getWebsiteMetaTitle(), $project->getWebsiteMetaDescription());
    }
}
