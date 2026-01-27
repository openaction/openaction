<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class WebsiteHomeMainImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;
    private int $width;
    private int $height;

    public function __construct(ImageManager $imageManager, string $sizeHomeMainImage)
    {
        $this->imageManager = $imageManager;
        [$this->width, $this->height] = explode('x', $sizeHomeMainImage);
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->read($file->getLocalContent());

        $data->scaleDown($this->width, $this->height);

        $canvas = $this->imageManager->create($data->width(), $data->height())->fill('ffffff');
        $canvas->place($data, 'center');

        $encoded = $canvas->toWebp(80);
        $file->setStorageContent((string) $encoded, 'webp');
    }
}
