<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class WebsiteContentImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->make($file->getLocalPathname());
        $data->orientate();

        $canvas = $this->imageManager->canvas($data->getWidth(), $data->getHeight(), 'ffffff');
        $canvas->insert($data, 'top-left');

        $canvas->resize(3000, 3000, static function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $canvas->encode('webp', quality: 80);
        $file->setStorageContent($canvas->getEncoded(), 'webp');
    }
}
