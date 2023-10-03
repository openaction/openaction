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
        $data = $this->imageManager->make($file->getLocalContent());
        $data->orientate();

        $data->resize($this->width, $this->height, static function ($constraint) {
            $constraint->aspectRatio();
        });

        $canvas = $this->imageManager->canvas($this->width, $this->height, 'ffffff');
        $canvas->insert($data, 'center');

        $canvas->encode('jpg');
        $file->setStorageContent($canvas->getEncoded(), 'jpg');
    }
}
