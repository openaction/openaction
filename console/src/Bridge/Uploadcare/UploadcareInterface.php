<?php

namespace App\Bridge\Uploadcare;

use App\Bridge\Uploadcare\Model\UploadKey;
use Symfony\Component\HttpFoundation\File\File;

interface UploadcareInterface
{
    public function downloadFile(string $uuid, string $extension): ?File;

    public function generateUploadKey(): UploadKey;
}
