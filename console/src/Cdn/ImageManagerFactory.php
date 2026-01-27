<?php

namespace App\Cdn;

use Intervention\Image\ImageManager;

final class ImageManagerFactory
{
    public function create(): ImageManager
    {
        return ImageManager::gd(autoOrientation: false);
    }
}
