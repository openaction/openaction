<?php

namespace App\Tests\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use App\Cdn\UploadHandler\WebsiteHomeMainImageHandler;
use App\Tests\UnitTestCase;
use Intervention\Image\ImageManager;

class WebsiteHomeMainImageHandlerTest extends UnitTestCase
{
    public function provideHandle()
    {
        yield 'mario.png' => [
            'local' => __DIR__.'/../../Fixtures/upload/mario.png',
            'storage' => 'upload/mario',
            'extension' => 'png',
            'expectedWidth' => 1192,
            'expectedHeight' => 1500,
        ];

        yield 'image-exif.jpg' => [
            'local' => __DIR__.'/../../Fixtures/upload/image-exif.jpg',
            'storage' => 'upload/image-exif',
            'extension' => 'jpg',
            'expectedWidth' => 1000,
            'expectedHeight' => 1500,
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
        $manager = new ImageManager();
        $handler = new WebsiteHomeMainImageHandler($manager, '2700x1500');

        $upload = new CdnUpload($local, $storage, $extension);
        $handler->handle($upload);

        $image = $manager->make($upload->getStorageContent());
        $this->assertSame($expectedWidth, $image->getWidth());
        $this->assertSame($expectedHeight, $image->getHeight());
    }
}
