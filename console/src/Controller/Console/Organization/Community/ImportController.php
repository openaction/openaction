<?php

namespace App\Controller\Console\Organization\Community;

use App\Bridge\Uploadcare\UploadcareInterface;
use App\Community\ImportExport\ContactImporter;
use App\Controller\AbstractController;
use App\Entity\Community\Import;
use App\Form\Community\ImportMetadataType;
use App\Form\Community\Model\ImportMetadataData;
use App\Platform\Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/community/contacts/import')]
class ImportController extends AbstractController
{
    public function __construct(private UploadcareInterface $uploadcare, private ContactImporter $importer)
    {
    }

    #[Route('', name: 'console_organization_community_contacts_import')]
    public function start()
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/organization/community/import/start.html.twig', [
            'uploadKey' => $this->uploadcare->generateUploadKey(),
        ]);
    }

    #[Route('/prepare', name: 'console_organization_community_contacts_import_prepare')]
    public function prepare(Request $request)
    {
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
            'redirectUrl' => $this->generateUrl('console_organization_community_contacts_import_columns', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
                'uuid' => $import->getUuid(),
            ]),
        ]);
    }

    #[Route('/{uuid}/columns', name: 'console_organization_community_contacts_import_columns')]
    public function columns(Import $import, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($import);

        $data = ImportMetadataData::createFromImport($import);

        $form = $this->createForm(ImportMetadataType::class, $data, ['organization' => $this->getOrganization()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->importer->startImport($import, $data);

            return $this->redirectToRoute('console_organization_community_contacts_import_progress', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
                'uuid' => $import->getUuid(),
            ]);
        }

        return $this->render('console/organization/community/import/columns.html.twig', [
            'import' => $import,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/progress', name: 'console_organization_community_contacts_import_progress')]
    public function progress(Import $import)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameOrganization($import);

        return $this->render('console/organization/community/import/progress.html.twig', [
            'import' => $import,
        ]);
    }
}
