<?php

namespace App\Cdn\Listener;

use App\Entity\Project;
use App\Entity\Upload;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;

class RemoveUploadedFileListener
{
    private FilesystemOperator $cdnStorage;
    private LoggerInterface $logger;

    public function __construct(FilesystemOperator $cdnStorage, LoggerInterface $logger)
    {
        $this->cdnStorage = $cdnStorage;
        $this->logger = $logger;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Upload) {
            $this->deleteFile($entity);
        } elseif ($entity instanceof Project) {
            $this->deleteDirectory($entity->getUuid());
        }
    }

    private function deleteFile(Upload $entity)
    {
        try {
            $this->cdnStorage->delete($entity->getPathname());
        } catch (\Exception $e) {
            $this->logger->error('Unable to remove uploaded file from storage', [
                'exception' => $e,
            ]);
        }
    }

    private function deleteDirectory(string $directory)
    {
        try {
            $this->cdnStorage->deleteDirectory($directory);
        } catch (\Exception $e) {
            $this->logger->error('Unable to remove directory "'.$directory.'" from storage', [
                'exception' => $e,
            ]);
        }
    }
}
