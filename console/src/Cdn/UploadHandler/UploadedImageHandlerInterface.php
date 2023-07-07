<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;

interface UploadedImageHandlerInterface
{
    public function handle(CdnUpload $file);
}
