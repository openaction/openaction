<?php

namespace App\Controller\Console\Project\Community;

use App\Bridge\Quorum\QuorumInterface;
use App\Controller\AbstractController;
use App\Entity\Community\Contact;
use App\Platform\Permissions;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationMemberRepository;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Search\CrmBatchManager;
use App\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/community/contacts')]
class ContactCrmController extends AbstractController
{
    public function __construct(
        private TagRepository $tagRepository,
        private OrganizationMemberRepository $memberRepository,
        private ContactRepository $contactRepository,
        private MessageBusInterface $bus,
        private QuorumInterface $quorum,
    ) {
    }

    #[Route('', name: 'console_community_contacts')]
    public function index()
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_VIEW, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $orga = $this->getOrganization();

        return $this->render('console/project/community/contacts/list.html.twig', [
            'organization_tags_names' => $this->tagRepository->findNamesByOrganization($orga),
            'organization_member' => $this->memberRepository->findMember($this->getUser(), $orga),
            'is_read_only' => !$this->isGranted(Permissions::COMMUNITY_CONTACTS_UPDATE, $this->getProject()),
        ]);
    }

    #[Route('/{uuid}/update-tags', name: 'console_community_contacts_update_tags', methods: ['POST'])]
    public function updateTags(Contact $contact, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_UPDATE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if (!$contact->isInProject($this->getProject())) {
            throw $this->createAccessDeniedException();
        }

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

    #[Route('/batch/add-tag', name: 'console_community_contacts_batch_add_tag', methods: ['POST'])]
    public function batchAddTag(CrmBatchManager $batchManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_UPDATE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if (!$job = $batchManager->addTagBatch($orga, $batchManager->parseBatchRequest($request))) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse([
            'statusUrl' => $this->generateUrl('console_api_job_status', ['id' => $job->getId()]),
        ]);
    }

    #[Route('/batch/remove-tag', name: 'console_community_contacts_batch_remove_tag', methods: ['POST'])]
    public function batchRemoveTag(CrmBatchManager $batchManager, Request $request)
    {
        $orga = $this->getOrganization();
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_CONTACTS_UPDATE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        if (!$job = $batchManager->removeTagBatch($orga, $batchManager->parseBatchRequest($request))) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse([
            'statusUrl' => $this->generateUrl('console_api_job_status', ['id' => $job->getId()]),
        ]);
    }
}
