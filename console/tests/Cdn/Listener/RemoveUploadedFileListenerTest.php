<?php

namespace App\Tests\Cdn\Listener;

use App\Cdn\CdnUploader;
use App\Cdn\Listener\RemoveUploadedFileListener;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Website\Document;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RemoveUploadedFileListenerTest extends KernelTestCase
{
    private ?RemoveUploadedFileListener $listener = null;
    private ?FilesystemOperator $storage = null;
    private ?EntityManagerInterface $em = null;
    private ?CdnUploader $uploader;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->listener = static::getContainer()->get(RemoveUploadedFileListener::class);
        $this->storage = static::getContainer()->get('cdn.storage');
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->uploader = new CdnUploader([], $this->storage, $this->em);
    }

    protected function tearDown(): void
    {
        $this->listener = null;
        $this->storage = null;
        $this->em = null;
        $this->uploader = null;
    }

    public function provideDocument()
    {
        yield [
            __DIR__.'/../../Fixtures/upload/document.pdf',
            'document.pdf',
            'document',
            'e816bcc6-0568-46d1-b0c5-917ce4810a87',
        ];
    }

    /**
     * @dataProvider provideDocument
     */
    public function testDocumentPreRemove(
        string $filename,
        string $originalName,
        string $name,
        string $projectUuid,
    ) {
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => $projectUuid]);

        $upload = $this->uploader->upload(
            CdnUploadRequest::createWebsiteDocumentRequest($project, new UploadedFile($filename, $originalName))
        );

        $document = new Document($project, $name, $upload);

        $this->em->persist($document);
        $this->em->flush();

        $pathname = $upload->getPathname();
        $this->assertTrue($this->storage->fileExists($pathname));

        $this->em->remove($document);
        $this->em->flush();

        $this->assertFalse($this->storage->fileExists($pathname));
    }

    /**
     * @dataProvider provideDocument
     */
    public function testProjectPreRemove(
        string $filename,
        string $originalName,
        string $name,
        string $projectUuid,
    ) {
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => $projectUuid]);

        $upload = $this->uploader->upload(
            CdnUploadRequest::createWebsiteDocumentRequest($project, new UploadedFile($filename, $originalName))
        );

        $document = new Document($project, $name, $upload);

        $this->em->persist($document);
        $this->em->flush();

        $pathname = $upload->getPathname();
        $this->assertTrue($this->storage->fileExists($pathname));

        $this->em->remove($project);
        $this->em->flush();

        $this->assertFalse($this->storage->fileExists($pathname));
    }
}
