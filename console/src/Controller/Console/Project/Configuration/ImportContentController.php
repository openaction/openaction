<?php

namespace App\Controller\Console\Project\Configuration;

use App\Bridge\Uploadcare\UploadcareInterface;
use App\Community\ImportExport\ContentImporter;
use App\Controller\AbstractController;
use App\Entity\Community\ContentImport;
use App\Entity\Community\Model\ContentImportSettings;
use App\Form\Project\ContentImportType;
use App\Platform\Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/content/import')]
class ImportContentController extends AbstractController
{
    public function __construct(private UploadcareInterface $uploadCare, private ContentImporter $importer)
    {
    }

    #[Route('', name: 'console_project_configuration_content_import')]
    public function start(): Response
    {
        // TODO! change to right permission
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/content_import/start.html.twig', [
            'uploadKey' => $this->uploadCare->generateUploadKey(),
        ]);
    }

    #[Route('/prepare', name: 'console_project_configuration_content_import_prepare')]
    public function prepare(Request $request): JsonResponse
    {
        // TODO! Change to right permissions
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        // Download file locally
        $file = $this->uploadCare->downloadFile(
            $request->query->get('fileUuid'),
            pathinfo($request->query->get('fileName'), PATHINFO_EXTENSION),
        );

        if (!$file) {
            throw $this->createNotFoundException();
        }

        // currently only WP content can be imported, so we set the import type accordingly
        // this can be extended with a form to select the import type before the file is uploaded
        $import = $this->importer->prepareImport($this->getProject(), $file, ContentImportSettings::IMPORT_SOURCE_WORDPRESS);

        // Provide redirect URL to JavaScript
        return new JsonResponse([
            'redirectUrl' => $this->generateUrl('console_project_configuration_content_import_settings', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $import->getUuid(),
            ]),
        ]);
    }

    #[Route('/{uuid}/settings', name: 'console_project_configuration_content_import_settings')]
    public function settings(ContentImport $import, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($import->getProject());

        $data = ContentImportSettings::createFromImport($import);
        $form = $this->createForm(ContentImportType::class, $data, ['import_source' => $import->getSource()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->importer->startImport($import, $form->getData());

            return $this->redirectToRoute('console_project_configuration_content_import_progress', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $import->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/content_import/settings.html.twig', [
            'import' => $import,
            'form' => $form,
        ]);
    }

    #[Route('/{uuid}/progress', name: 'console_project_configuration_content_import_progress')]
    public function progress(ContentImport $import)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($import->getProject());

        return $this->render('console/project/configuration/content_import/progress.html.twig', [
            'import' => $import,
        ]);
    }
}
