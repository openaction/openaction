<?php

namespace App\Tests\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use App\Cdn\UploadHandler\ProjectLogoImageHandler;
use App\Tests\UnitTestCase;
use Intervention\Image\ImageManager;

class ProjectLogoImageHandlerTest extends UnitTestCase
{
    public function provideHandle()
    {
        yield 'mario.png' => [
            'local' => __DIR__.'/../../Fixtures/upload/mario.png',
            'storage' => 'upload/mario',
            'extension' => 'png',
            'expectedWidth' => 318,
            'expectedHeight' => 400,
        ];

        yield 'image-exif.jpg' => [
            'local' => __DIR__.'/../../Fixtures/upload/image-exif.jpg',
            'storage' => 'upload/image-exif',
            'extension' => 'jpg',
            'expectedWidth' => 267,
            'expectedHeight' => 400,
        ];

        yield 'image.webp' => [
            'local' => __DIR__.'/../../Fixtures/upload/image.webp',
            'storage' => 'upload/image-webp',
            'extension' => 'webp',
            'expectedWidth' => 792,
            'expectedHeight' => 400,
        ];
    }

    /**
     * @dataProvider provideHandle
     */
    public function testHandle(string $local, string $storage, string $extension, int $expectedWidth, int $expectedHeight)
    {
        $manager = ImageManager::gd(autoOrientation: false);
        $handler = new ProjectLogoImageHandler($manager);

        $upload = new CdnUpload($local, $storage, $extension);
        $handler->handle($upload);

        $image = $manager->read($upload->getStorageContent());
        $this->assertSame($expectedWidth, $image->width());
        $this->assertSame($expectedHeight, $image->height());
    }
}
