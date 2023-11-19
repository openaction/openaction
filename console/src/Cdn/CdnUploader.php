<?php

namespace App\Cdn;

use App\Cdn\Model\CdnUpload;
use App\Cdn\Model\CdnUploadRequest;
use App\Cdn\UploadHandler\UploadedImageHandlerInterface;
use App\Entity\Upload;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;

class CdnUploader
{
    private EntityManagerInterface $manager;
    private FilesystemOperator $cdnStorage;

    /**
     * @var UploadedImageHandlerInterface[]
     */
    private array $handlers = [];

    /**
     * @param UploadedImageHandlerInterface[] $handlers
     */
    public function __construct(iterable $handlers, FilesystemOperator $cdnStorage, EntityManagerInterface $manager)
    {
        $this->cdnStorage = $cdnStorage;
        $this->manager = $manager;

        foreach ($handlers as $handler) {
            $this->handlers[\get_class($handler)] = $handler;
        }
    }

    public function upload(CdnUploadRequest $request): Upload
    {
        $upload = $this->createUploadFromRequest($request);

        // Optimize the file before uploading it
        if ($handler = $this->handlers[$request->getHandler()] ?? null) {
            $handler->handle($upload);
        }

        // Save it into the CDN
        $this->cdnStorage->write($upload->getStorageFullPath(), $upload->getStorageContent());

        // Save a reference in the database
        $upload = new Upload($upload->getStorageFullPath(), $request->getProject());

        $this->manager->persist($upload);
        $this->manager->flush();

        return $upload;
    }

    public function duplicate(Upload $upload): ?Upload
    {
        // If the original Upload was removed in the meantime, do not duplicate it
        if (!$this->cdnStorage->fileExists($upload->getPathname())) {
            return null;
        }

        $pathinfo = pathinfo($upload->getPathname());
        $newPathname = $pathinfo['dirname'].'/'.$pathinfo['filename'].'-copy-'.date('Y-m-d-H-i').'-'.substr(md5(random_bytes(10)), 0, 6).'.'.$pathinfo['extension'];

        // Save it into the CDN
        $this->cdnStorage->write($newPathname, $this->cdnStorage->read($upload->getPathname()));

        $duplicated = new Upload($newPathname, $upload->getProject());

        $this->manager->persist($duplicated);
        $this->manager->flush();

        return $duplicated;
    }

    private function createUploadFromRequest(CdnUploadRequest $request): CdnUpload
    {
        $filename = $request->getFilename() ?: Uid::random()->toRfc4122();

        $basePath = '';
        if ($request->getProject()) {
            $basePath = $request->getProject()->getUuid()->toRfc4122().'/';
        }

        return new CdnUpload(
            $request->getFile()->getPathname(),
            $basePath.$request->getDirectory().'/'.$filename,
            $request->getFile()->guessExtension() ?: 'txt'
        );
    }
}
