<?php

namespace App\Tests\Cdn;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Tests\UnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CdnUploaderTest extends UnitTestCase
{
    public function provideFiles()
    {
        yield 'document.pdf' => [
            __DIR__.'/../Fixtures/upload/document.pdf',
            'document.pdf',
            'document',
            true,
            '15016b0b-3ed6-5630-a167-3dff4ddf43ac/document/file.pdf',
        ];

        yield 'french.jpg' => [
            __DIR__.'/../Fixtures/upload/french.jpg',
            'french.jpg',
            'content-image',
            true,
            '15016b0b-3ed6-5630-a167-3dff4ddf43ac/content-image/file.jpg',
        ];

        yield 'mario.png' => [
            __DIR__.'/../Fixtures/upload/mario.png',
            'mario.png',
            'post-image',
            true,
            '15016b0b-3ed6-5630-a167-3dff4ddf43ac/post-image/file.png',
        ];

        yield 'penguin.bmp' => [
            __DIR__.'/../Fixtures/upload/penguin.bmp',
            'penguin.bmp',
            'user-picture',
            false,
            'user-picture/file.bmp',
        ];

        yield 'image.webp' => [
            __DIR__.'/../Fixtures/upload/image.webp',
            'image.webp',
            'content-image',
            true,
            '15016b0b-3ed6-5630-a167-3dff4ddf43ac/content-image/file.webp',
        ];
    }

    /**
     * @dataProvider provideFiles
     */
    public function testUpload(string $filename, string $originalName, string $context, bool $withProject, string $expectedPath)
    {
        /** @var EntityManagerInterface|MockObject $manager */
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->expects($this->once())->method('persist');

        $storage = new Filesystem(new InMemoryFilesystemAdapter());

        $uploadedFile = new UploadedFile($filename, $originalName);
        $uploader = new CdnUploader([], $storage, $manager);

        $project = $withProject ? $this->createProject(1) : null;
        $upload = $uploader->upload(new CdnUploadRequest($uploadedFile, $context, null, $project, 'file'));

        // Should have been persisted in the CDN
        $this->assertTrue($storage->fileExists($expectedPath));
        $this->assertStringEqualsFile($filename, $storage->read($expectedPath));

        // Should have created a reference in the database
        $this->assertSame($expectedPath, $upload->getPathname());
    }

    public function testDuplicate()
    {
        /** @var EntityManagerInterface|MockObject $manager */
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->expects($this->exactly(2))->method('persist');

        $storage = new Filesystem(new InMemoryFilesystemAdapter());

        $uploadedFile = new UploadedFile(__DIR__.'/../Fixtures/upload/french.jpg', 'french.jpg');
        $uploader = new CdnUploader([], $storage, $manager);

        $upload = $uploader->upload(new CdnUploadRequest($uploadedFile, 'post-image', null, $this->createProject(1), 'file'));
        $duplicated = $uploader->duplicate($upload);
        $this->assertNotSame($upload->getPathname(), $duplicated->getPathname());

        // Should have been persisted in the CDN
        $this->assertTrue($storage->fileExists($duplicated->getPathname()));
        $this->assertStringEqualsFile($uploadedFile->getPathname(), $storage->read($duplicated->getPathname()));
    }
}
