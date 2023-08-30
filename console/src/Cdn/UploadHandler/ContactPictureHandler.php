<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class ContactPictureHandler implements UploadedImageHandlerInterface
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

        $data->resize(800, 800, static function ($constraint) {
            $constraint->aspectRatio();
        });

        $canvas = $this->imageManager->canvas(800, 800, 'ffffff');
        $canvas->insert($data, 'center');

        $canvas->encode('jpg');
        $file->setStorageContent($canvas->getEncoded(), 'jpg');
    }
}
