<?php

namespace App\Community\ImportExport;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Community\ImportExport\Consumer\ImportMessage;
use App\Entity\Community\ContentImport;
use App\Entity\Community\Model\ContentImportSettings;
use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\MessageBusInterface;

class ContentImporter
{
    private EntityManagerInterface $em;
    private CdnUploader $uploader;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $em, CdnUploader $u, MessageBusInterface $bus)
    {
        $this->em = $em;
        $this->uploader = $u;
        $this->bus = $bus;
    }

    public function prepareImport(Organization $organization, File $file, string $source): ContentImport
    {
        $upload = $this->uploader->upload(CdnUploadRequest::createOrganizationPrivateFileRequest($file));

        $import = new ContentImport($organization, $upload, $source);

        $this->em->persist($import);
        $this->em->flush();

        return $import;
    }

    public function startImport(ContentImport $import, ContentImportSettings $settings): void
    {
        $import->setSelectedSettings($settings);

        $this->em->persist($import);
        $this->em->flush();

        $this->bus->dispatch(new ImportMessage($import->getId()));
    }
}
