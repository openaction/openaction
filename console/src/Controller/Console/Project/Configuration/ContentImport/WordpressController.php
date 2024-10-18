<?php

namespace App\Controller\Console\Project\Configuration\ContentImport;

use App\Bridge\Uploadcare\UploadcareInterface;
use App\Controller\AbstractController;
use App\Entity\Community\ContentImport;
use App\Entity\Community\Model\ContentImportSettings;
use App\Form\Project\ContentImportType;
use App\Platform\Permissions;
use App\Repository\Website\TrombinoscopePersonRepository;
use App\Website\ImportExport\ContentImporter;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/content-import/wordpress')]
class WordpressController extends AbstractController
{
    public function __construct(
        private readonly UploadcareInterface $uploadCare,
        private readonly ContentImporter $importer
    ) {
    }

    #[Route('', name: 'console_project_configuration_content_import_wordpress')]
    public function start(): Response
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/content_import/wordpress/start.html.twig', [
            'uploadKey' => $this->uploadCare->generateUploadKey(),
        ]);
    }

    #[Route('/prepare', name: 'console_project_configuration_content_import_wordpress_prepare')]
    public function prepare(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
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
            'redirectUrl' => $this->generateUrl('console_project_configuration_content_import_wordpress_settings', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $import->getUuid(),
            ]),
        ]);
    }

    #[Route('/{uuid}/settings', name: 'console_project_configuration_content_import_wordpress_settings')]
    public function settings(TrombinoscopePersonRepository $trombinoscopeRepository, ContentImport $import, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($import->getProject());

        $data = ContentImportSettings::createFromImport($import);

        $form = $this->createForm(ContentImportType::class, $data, ['import_source' => $import->getSource()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->importer->startImport($import, $form->getData());

            return $this->redirectToRoute('console_project_configuration_content_import_wordpress_progress', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $import->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/content_import/wordpress/settings.html.twig', [
            'import' => $import,
            'availableAuthors' => $trombinoscopeRepository->getProjectPersonsList($this->getProject(), Query::HYDRATE_ARRAY),
            'form' => $form,
        ]);
    }

    #[Route('/{uuid}/progress', name: 'console_project_configuration_content_import_wordpress_progress')]
    public function progress(ContentImport $import)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($import->getProject());

        return $this->render('console/project/configuration/content_import/wordpress/progress.html.twig', [
            'import' => $import,
        ]);
    }
}
