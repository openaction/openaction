<?php

namespace App\Controller\Console\Project\Configuration;

use App\Bridge\Uploadcare\UploadcareInterface;
use App\Community\ImportExport\ContentImporter;
use App\Controller\AbstractController;
use App\Entity\Community\ContentImport;
use App\Platform\Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/content/import')]
class ImportContentController extends AbstractController
{
    public function __construct(private UploadcareInterface $uploadcare, private ContentImporter $importer)
    {
    }

    #[Route('', name: 'console_project_configuration_content_import')]
    public function start()
    {
        // TODO! change to right permission
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/content_import/start.html.twig', [
            'uploadKey' => $this->uploadcare->generateUploadKey(),
        ]);
    }

    #[Route('/prepare', name: 'console_project_configuration_content_import_prepare')]
    public function prepare(Request $request)
    {
        // TODO! Change to right permissions
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        // Download file locally
        $file = $this->uploadcare->downloadFile(
            $request->query->get('fileUuid'),
            pathinfo($request->query->get('fileName'), PATHINFO_EXTENSION),
        );

        if (!$file) {
            throw $this->createNotFoundException();
        }

        // Prepare import
        $import = $this->importer->prepareImport($this->getOrganization(), $file);

        // Provide redirect URL to JavaScript
        return new JsonResponse([
            'redirectUrl' => $this->generateUrl('console_project_configuration_content_import_settings', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $import->getUuid(),
            ]),
        ]);
    }

    #[Route('/{uuid}/settings', name: 'console_project_configuration_content_import_settings')]
    public function settings(ContentImport $import, Request $request)
    {
        // TODO! Change to right permissions
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($import);

        return $this->render('console/project/configuration/content_import/settings.html.twig', [
            'import' => $import,
            'form' => '',
        ]);
    }
}
