<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class WebsiteTrombinoscopeMainImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;
    private int $width;
    private int $height;

    public function __construct(ImageManager $imageManager, string $sizeTrombinoscopeMainImage)
    {
        $this->imageManager = $imageManager;
        [$this->width, $this->height] = explode('x', $sizeTrombinoscopeMainImage);
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->read($file->getLocalContent());

        $data->scale($this->width, $this->height);

        $canvas = $this->imageManager->create($this->width, $this->height)->fill('ffffff');
        $canvas->place($data, 'center');

        $encoded = $canvas->toWebp(80);
        $file->setStorageContent((string) $encoded, 'webp');
    }
}
