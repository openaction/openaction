<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class EmailingContentImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->read($file->getLocalContent());

        $canvas = $this->imageManager->create($data->width(), $data->height())->fill('ffffff');
        $canvas->place($data, 'top-left');

        $canvas->scaleDown(2000, 2000);

        $encoded = $canvas->toJpeg(80);
        $file->setStorageContent((string) $encoded, 'jpg');
    }
}
