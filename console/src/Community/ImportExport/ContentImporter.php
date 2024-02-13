<?php

namespace App\Community\ImportExport;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Entity\Community\ContentImport;
use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class ContentImporter
{
    private EntityManagerInterface $em;
    private CdnUploader $uploader;

    public function __construct(EntityManagerInterface $em, CdnUploader $u)
    {
        $this->em = $em;
        $this->uploader = $u;
    }

    public function prepareImport(Organization $organization, File $file): ContentImport
    {
        $upload = $this->uploader->upload(CdnUploadRequest::createOrganizationPrivateFileRequest($file));

        $import = new ContentImport($organization, $upload);

        $this->em->persist($import);
        $this->em->flush();

        return $import;
    }
}
