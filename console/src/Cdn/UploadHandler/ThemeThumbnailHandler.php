<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class ThemeThumbnailHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->make($file->getLocalContent());
        $data->orientate();

        $data->resize(450, 400, static function ($constraint) {
            $constraint->aspectRatio();
        });

        $canvas = $this->imageManager->canvas(450, 400, 'ffffff');
        $canvas->insert($data, 'center');

        $canvas->encode('webp', quality: 80);
        $file->setStorageContent($canvas->getEncoded(), 'webp');
    }
}
