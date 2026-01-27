<?php

namespace App\Tests\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use App\Cdn\UploadHandler\WebsiteContentImageHandler;
use App\Tests\UnitTestCase;
use Intervention\Image\ImageManager;

class WebsiteContentImageHandlerTest extends UnitTestCase
{
    public function provideHandle()
    {
        yield 'mario.png' => [
            'local' => __DIR__.'/../../Fixtures/upload/mario.png',
            'storage' => 'upload/mario',
            'extension' => 'png',
            'expectedWidth' => 1473,
            'expectedHeight' => 1854,
        ];

        yield 'image-exif.jpg' => [
            'local' => __DIR__.'/../../Fixtures/upload/image-exif.jpg',
            'storage' => 'upload/image-exif',
            'extension' => 'jpg',
            'expectedWidth' => 1800,
            'expectedHeight' => 1200,
        ];

        yield 'image.webp' => [
            'local' => __DIR__.'/../../Fixtures/upload/image.webp',
            'storage' => 'upload/image-webp',
            'extension' => 'webp',
            'expectedWidth' => 2000,
            'expectedHeight' => 1011,
        ];
    }

    /**
     * @dataProvider provideHandle
     */
    public function testHandle(string $local, string $storage, string $extension, int $expectedWidth, int $expectedHeight)
    {
        $manager = ImageManager::gd(autoOrientation: false);
        $handler = new WebsiteContentImageHandler($manager);

        $upload = new CdnUpload($local, $storage, $extension);
        $handler->handle($upload);

        $image = $manager->read($upload->getStorageContent());
        $this->assertSame($expectedWidth, $image->width());
        $this->assertSame($expectedHeight, $image->height());
    }
}
