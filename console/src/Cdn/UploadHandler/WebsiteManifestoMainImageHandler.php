<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class WebsiteManifestoMainImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;
    private int $width;
    private int $height;

    public function __construct(ImageManager $imageManager, string $sizeManifestoMainImage)
    {
        $this->imageManager = $imageManager;
        [$this->width, $this->height] = explode('x', $sizeManifestoMainImage);
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->read($file->getLocalContent());

        $canvas = $this->imageManager->create($data->width(), $data->height())->fill('ffffff');
        $canvas->place($data, 'top-left');
        $canvas->cover($this->width, $this->height);

        $encoded = $canvas->toWebp(80);
        $file->setStorageContent((string) $encoded, 'webp');
    }
}
