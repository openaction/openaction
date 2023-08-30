<?php

namespace App\Controller\Console\Project\Community;

use App\Community\Consumer\StartPhoningCampaignMessage;
use App\Community\ContactViewBuilder;
use App\Controller\AbstractController;
use App\DataManager\PhoningCampaignDataManager;
use App\Entity\Community\PhoningCampaign;
use App\Form\Community\Model\PhoningCampaignMetaData;
use App\Form\Community\PhoningCampaignMetaDataType;
use App\Platform\Permissions;
use App\Proxy\DomainRouter;
use App\Repository\Community\PhoningCampaignRepository;
use App\Repository\Community\PhoningCampaignTargetRepository;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/community/phoning')]
class PhoningController extends AbstractController
{
    private PhoningCampaignRepository $repository;
    private EntityManagerInterface $em;
    private ContactViewBuilder $contactViewBuilder;

    public function __construct(PhoningCampaignRepository $r, EntityManagerInterface $em, ContactViewBuilder $cvb)
    {
        $this->repository = $r;
        $this->em = $em;
        $this->contactViewBuilder = $cvb;
    }

    #[Route('', name: 'console_community_phoning')]
    public function index(PhoningCampaignTargetRepository $targetRepository, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_DRAFTS, $project);
        $this->denyIfSubscriptionExpired();

        $activeCampaigns = $this->repository->findAllActive($project);
        $activeCampaignsProgress = $targetRepository->findActiveCampaignsProgress($project);

        $currentPage = $request->query->getInt('p', 1);
        $finishedCampaigns = $this->repository->findAllFinishedPaginator($project, $currentPage);
        $finishedCampaignsProgress = $targetRepository->findFinishedCampaignsProgress($project);

        return $this->render('console/project/community/phoning/index.html.twig', [
            'project' => $project,
            'campaigns_drafts' => $this->repository->findAllDrafts($project),
            'campaigns_active' => $activeCampaigns,
            'campaigns_active_progress' => $activeCampaignsProgress,
            'current_page' => $currentPage,
            'campaigns_finished' => $finishedCampaigns,
            'campaigns_finished_progress' => $finishedCampaignsProgress,
            'items_per_page' => 30,
        ]);
    }

    #[Route('/create', name: 'console_community_phoning_create')]
    public function create(TranslatorInterface $translator, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $campaign = new PhoningCampaign($this->getProject(), $translator->trans('create.name', [], 'project_phoning'));

        $this->em->persist($campaign);
        $this->em->flush();

        return $this->redirectToRoute('console_community_phoning_metadata', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $campaign->getUuid(),
        ]);
    }

    #[Route('/{uuid}/duplicate', name: 'console_community_phoning_duplicate')]
    public function duplicate(PhoningCampaignDataManager $dataManager, PhoningCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $duplicated = $dataManager->duplicate($campaign);

        return $this->redirectToRoute('console_community_phoning_metadata', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $duplicated->getUuid(),
        ]);
    }

    #[Route('/{uuid}/metadata', name: 'console_community_phoning_metadata', methods: ['GET', 'POST'])]
    public function metadata(PhoningCampaignRepository $repo, PhoningCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        // Active and finished campaigns can't be edited anymore
        if ($campaign->isActive() || $campaign->isFinished()) {
            throw $this->createAccessDeniedException();
        }

        $metadata = PhoningCampaignMetaData::createFromCampaign($campaign);

        $form = $this->createForm(PhoningCampaignMetaDataType::class, $metadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->applyMetadataUpdate($metadata);
            $repo->updateFilters($campaign, $metadata);

            $this->em->persist($campaign);
            $this->em->flush();

            $this->addFlash('success', 'phoning.metadata_updated_success');

            return $this->redirectToRoute('console_community_phoning_metadata', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $campaign->getUuid(),
            ]);
        }

        return $this->render('console/project/community/phoning/metadata.html.twig', [
            'campaign' => $campaign,
            'project' => $this->getProject(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/start', name: 'console_community_phoning_start', methods: ['GET'])]
    public function start(MessageBusInterface $bus, Request $request, PhoningCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_ACTIVE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameProject($campaign);

        $campaign->start();

        $this->em->persist($campaign);
        $this->em->flush();

        $bus->dispatch(new StartPhoningCampaignMessage($campaign->getId()));

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_community_phoning', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/{uuid}/stop', name: 'console_community_phoning_stop', methods: ['GET'])]
    public function stop(Request $request, PhoningCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_ACTIVE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameProject($campaign);

        $campaign->stop();

        $this->em->persist($campaign);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_community_phoning', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/{uuid}/delete', name: 'console_community_phoning_delete', methods: ['GET'])]
    public function delete(Request $request, PhoningCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameProject($campaign);

        // Deleting an active or finished PhoningCampaign is forbidden
        if ($campaign->isActive() || $campaign->isFinished()) {
            throw $this->createAccessDeniedException();
        }

        $this->em->remove($campaign);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_community_phoning', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/{uuid}/filter-preview', name: 'console_community_phoning_filter_preview', methods: ['GET'])]
    public function filterPreviewCount(PhoningCampaign $campaign, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_ACTIVE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        $emails = array_filter(explode(' ', trim($request->query->get('contacts'))));
        array_walk($emails, static function (&$email) {
            $email = strtolower($email);
        });

        return new JsonResponse([
            'count' => $this->contactViewBuilder
                ->onlyCallsSubscribers()
                ->havingParsedPhone()
                ->inProject($campaign->getProject())
                ->onlyMembers((bool) $request->query->get('member'))
                ->inAreas(array_filter(explode(' ', trim($request->query->get('areas')))))
                ->withTags(
                    array_filter(explode(' ', trim($request->query->get('tags')))),
                    trim($request->query->get('tagsType', 'or')),
                )
                ->withEmails($emails)
                ->count(),
        ]);
    }

    #[Route('/{uuid}/view', name: 'console_community_phoning_view')]
    public function view(DomainRouter $domainRouter, PhoningCampaign $campaign)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_PHONING_MANAGE_ACTIVE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        return $this->redirect($domainRouter->generateRedirectUrl($this->getProject(), 'phoning', Uid::toBase62($campaign->getUuid())));
    }
}
