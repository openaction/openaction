<?php

namespace App\Api\Payload\Admin;

use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Project;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateMainImagePayload
{
    #[Assert\NotBlank]
    #[Assert\Image(maxSize: '20Mi', mimeTypes: ['image/bmp', 'image/x-ms-bmp', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $file = null;

    public function buildUploadRequestFor(Project $project): CdnUploadRequest
    {
        return CdnUploadRequest::createWebsiteContentMainImageRequest($project, $this->file);
    }
}
