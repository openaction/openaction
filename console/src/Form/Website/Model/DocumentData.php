<?php

namespace App\Form\Website\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class DocumentData
{
    /**
     *     "application/msword",
     *     "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *     "application/vnd.ms-excel",
     *     "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *     "application/vnd.oasis.opendocument.text",
     *     "application/vnd.oasis.opendocument.spreadsheet",.
     *
     *     "image/jpeg",
     *     "image/png",
     *     "image/svg+xml"
     * })
     */
    #[Assert\NotBlank]
    #[Assert\File(maxSize: '20Mi', mimeTypes: ['application/pdf', 'image/jpeg', 'image/png', 'image/svg+xml'])]
    public ?UploadedFile $file = null;
}
