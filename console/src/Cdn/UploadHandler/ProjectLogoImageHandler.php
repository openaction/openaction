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
        $canvas = $this->imageManager->make($file->getLocalContent());
        $canvas->orientate();

        $canvas->resize(400, 200, static function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $canvas->encode('png');
        $file->setStorageContent($canvas->getEncoded(), 'png');
    }
}
