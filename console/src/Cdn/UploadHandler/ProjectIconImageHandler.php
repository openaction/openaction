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
        $data = $this->imageManager->read($file->getLocalContent());

        $data->scaleDown(256, 256);

        $canvas = $this->imageManager->create(256, 256);
        $canvas->place($data, 'center');

        $encoded = $canvas->toPng();
        $file->setStorageContent((string) $encoded, 'png');
    }
}
