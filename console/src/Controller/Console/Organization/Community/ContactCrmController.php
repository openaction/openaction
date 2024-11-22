<?php

namespace App\Controller\Console\Organization\Community;

use App\Bridge\Quorum\QuorumInterface;
use App\Controller\AbstractController;
use App\Entity\Community\Contact;
use App\Entity\Upload;
use App\Platform\Permissions;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationMemberRepository;
use App\Repository\ProjectRepository;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Search\CrmBatchManager;
use App\Util\Json;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/console/organization/{organizationUuid}/community/contacts')]
class ContactCrmController extends AbstractController
{
    public function __construct(
        private ContactRepository $contactRepository,
        private TagRepository $tagRepository,
        private OrganizationMemberRepository $memberRepository,
        private ProjectRepository $projectRepository,
        private MessageBusInterface $bus,
        private QuorumInterface $quorum,
    ) {
    }

    #[Route('', name: 'console_organization_community_contacts')]
    public function index(Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $tagFilter = $request->query->getInt('tag');

        return $this->render('console/organization/community/contacts/list.html.twig', [
            'tag_filter' => $tagFilter ? $this->tagRepository->find($tagFilter)?->getName() : null,
            'organization_tags_names' => $this->tagRepository->findNamesByOrganization($orga),
            'organization_member' => $this->memberRepository->findMember($this->getUser(), $orga),
            'organization_projects_names' => $this->projectRepository->createCrmNamesRegistry($orga),
        ]);
    }

    #[Route('/{uuid}/update-tags', name: 'console_organization_community_contacts_update_tags', methods: ['POST'])]
    public function updateTags(Contact $contact, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameOrganization($contact);

        try {
            $data = Json::decode($request->getContent());
        } catch (\Exception) {
            throw $this->createNotFoundException();
        }

        $this->contactRepository->updateTags($contact, $data);

        // Update CRM search index
        $this->bus->dispatch(UpdateCrmDocumentsMessage::forContact($contact));

        // Sync Quorum
        $this->quorum->persist($contact);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/batch/add-tag', name: 'console_organization_community_contacts_batch_add_tag', methods: ['POST'])]
    public function batchAddTag(CrmBatchManager $batchManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if (!$job = $batchManager->addTagBatch($orga, $batchManager->parseBatchRequest($request))) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse([
            'statusUrl' => $this->generateUrl('console_api_job_status', ['id' => $job->getId()]),
        ]);
    }

    #[Route('/batch/remove-tag', name: 'console_organization_community_contacts_batch_remove_tag', methods: ['POST'])]
    public function batchRemoveTag(CrmBatchManager $batchManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if (!$job = $batchManager->removeTagBatch($orga, $batchManager->parseBatchRequest($request))) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse([
            'statusUrl' => $this->generateUrl('console_api_job_status', ['id' => $job->getId()]),
        ]);
    }

    #[Route('/batch/export', name: 'console_organization_community_contacts_batch_export', methods: ['POST'])]
    public function batchExport(CrmBatchManager $batchManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if (!$job = $batchManager->exportBatch($orga, $batchManager->parseBatchRequest($request))) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse([
            'statusUrl' => $this->generateUrl('console_api_job_status', ['id' => $job->getId()]),
        ]);
    }

    #[Route('/batch/remove', name: 'console_organization_community_contacts_batch_remove', methods: ['POST'])]
    public function batchRemove(CrmBatchManager $batchManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $orga);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if (!$job = $batchManager->removeBatch($orga, $batchManager->parseBatchRequest($request))) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse([
            'statusUrl' => $this->generateUrl('console_api_job_status', ['id' => $job->getId()]),
        ]);
    }

    #[Route('/batch/export/download/{pathname}', requirements: ['pathname' => '.+'], name: 'console_organization_community_contacts_batch_export_download')]
    public function batchExportDownload(FilesystemReader $cdnStorage, SluggerInterface $slugger, Upload $upload)
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
