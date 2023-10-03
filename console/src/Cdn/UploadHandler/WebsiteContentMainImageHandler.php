<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class WebsiteContentMainImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;
    private int $width;
    private int $height;

    public function __construct(ImageManager $imageManager, string $sizeContentMainImage)
    {
        $this->imageManager = $imageManager;
        [$this->width, $this->height] = explode('x', $sizeContentMainImage);
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->make($file->getLocalContent());
        $data->orientate();

        $canvas = $this->imageManager->canvas($data->getWidth(), $data->getHeight(), 'ffffff');
        $canvas->insert($data, 'top-left');
        $canvas->fit($this->width, $this->height);

        $canvas->encode('jpg');
        $file->setStorageContent($canvas->getEncoded(), 'jpg');
    }
}
