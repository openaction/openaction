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
        $data = $this->imageManager->make($file->getLocalContent());
        $data->orientate();

        $data->resize($this->width, $this->height, static function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $canvas = $this->imageManager->canvas($data->getWidth(), $data->getHeight(), 'ffffff');
        $canvas->insert($data, 'center');

        $canvas->encode('webp', quality: 80);
        $file->setStorageContent($canvas->getEncoded(), 'webp');
    }
}
