<?php

namespace App\Controller\Console\Organization\Community;

use App\Community\ImportExport\ContactExporter;
use App\Controller\AbstractController;
use App\Entity\Upload;
use App\Platform\Permissions;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/console/organization/{organizationUuid}/community/contacts')]
class ExportController extends AbstractController
{
    #[Route('/export', name: 'console_organization_community_contacts_export')]
    public function export(ContactExporter $exporter, Request $request)
    {
        $orga = $this->getOrganization();

        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        $exporter->requestExport($this->getUser(), $orga, $request->query->getInt('tag') ?: null);

        $this->addFlash('success', 'export.success');

        return $this->redirectToRoute('console_organization_community_contacts', [
            'organizationUuid' => $orga->getUuid(),
        ]);
    }

    #[Route('/export/download/{pathname}', requirements: ['pathname' => '.+'], name: 'console_organization_community_contacts_export_download')]
    public function download(FilesystemReader $cdnStorage, SluggerInterface $slugger, Upload $upload)
    {
        $orga = $this->getOrganization();

        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();

        $path = $upload->getPathname();

        if (!$cdnStorage->fileExists($path)) {
            throw $this->createNotFoundException();
        }

        $response = new StreamedResponse(
            static function () use ($cdnStorage, $path) {
                stream_copy_to_stream($cdnStorage->readStream($path), fopen('php://output', 'wb'));
            }
        );

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            date('Y-m-d').'-'.$slugger->slug($orga->getName())->lower().'-contacts.xlsx'
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
