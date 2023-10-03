<?php

namespace App\Tests\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use App\Cdn\UploadHandler\WebsiteContentMainImageHandler;
use App\Tests\UnitTestCase;
use Intervention\Image\ImageManager;

class WebsiteContentMainImageHandlerTest extends UnitTestCase
{
    public function provideHandle()
    {
        yield 'mario.png' => [
            'local' => __DIR__.'/../../Fixtures/upload/mario.png',
            'storage' => 'upload/mario',
            'extension' => 'png',
            'expectedWidth' => 1700,
            'expectedHeight' => 1080,
        ];

        yield 'image-exif.jpg' => [
            'local' => __DIR__.'/../../Fixtures/upload/image-exif.jpg',
            'storage' => 'upload/image-exif',
            'extension' => 'jpg',
            'expectedWidth' => 1700,
            'expectedHeight' => 1080,
        ];

        yield 'image.webp' => [
            'local' => __DIR__.'/../../Fixtures/upload/image.webp',
            'storage' => 'upload/image-webp',
            'extension' => 'webp',
            'expectedWidth' => 1700,
            'expectedHeight' => 1080,
        ];
    }

    /**
     * @dataProvider provideHandle
     */
    public function testHandle(string $local, string $storage, string $extension, int $expectedWidth, int $expectedHeight)
    {
        $manager = new ImageManager();
        $handler = new WebsiteContentMainImageHandler($manager, '1700x1080');

        $upload = new CdnUpload($local, $storage, $extension);
        $handler->handle($upload);

        $image = $manager->make($upload->getStorageContent());
        $this->assertSame($expectedWidth, $image->getWidth());
        $this->assertSame($expectedHeight, $image->getHeight());
    }
}
