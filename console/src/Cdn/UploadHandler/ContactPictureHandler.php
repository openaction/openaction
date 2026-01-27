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
        $data = $this->imageManager->read($file->getLocalContent());

        $data->scale(800, 800);

        $canvas = $this->imageManager->create(800, 800)->fill('ffffff');
        $canvas->place($data, 'center');

        $encoded = $canvas->toWebp(80);
        $file->setStorageContent((string) $encoded, 'webp');
    }
}
