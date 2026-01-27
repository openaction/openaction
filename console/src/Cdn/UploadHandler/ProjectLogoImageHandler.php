<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class ProjectLogoImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function handle(CdnUpload $file)
    {
        $canvas = $this->imageManager->read($file->getLocalContent());

        $scale = min(800 / $canvas->width(), 400 / $canvas->height(), 1);
        $targetWidth = (int) ceil($canvas->width() * $scale);
        $targetHeight = (int) ceil($canvas->height() * $scale);
        $canvas->resize($targetWidth, $targetHeight);

        $encoded = $canvas->toPng();
        $file->setStorageContent((string) $encoded, 'png');
    }
}
