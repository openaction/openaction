<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class ProjectIconImageHandler implements UploadedImageHandlerInterface
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

        $data->resize(256, 256, static function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $canvas = $this->imageManager->canvas(256, 256);
        $canvas->insert($data, 'center');

        $canvas->encode('png');
        $file->setStorageContent($canvas->getEncoded(), 'png');
    }
}
