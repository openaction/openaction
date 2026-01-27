<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class WebsiteImportedImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function handle(CdnUpload $file)
    {
        $canvas = $this->imageManager->read($file->getLocalContent());

        $canvas->scaleDown(1800, 1800);

        $encoded = $canvas->toPng();
        $file->setStorageContent((string) $encoded, 'png');
    }
}
