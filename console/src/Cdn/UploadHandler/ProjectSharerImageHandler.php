<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class ProjectSharerImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->read($file->getLocalContent());

        $data->scaleDown(1200, 630);

        $canvas = $this->imageManager->create(1200, 630);
        $canvas->place($data, 'center');

        $encoded = $canvas->toPng();
        $file->setStorageContent((string) $encoded, 'png');
    }
}
