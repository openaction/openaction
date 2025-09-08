<?php

namespace App\Controller\Console\Project\Community\Texting;

use App\Controller\AbstractController;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Community\TextingCampaign;
use App\Form\Community\Model\TextingCampaignMetaData;
use App\Form\Community\TextingCampaignMetaDataType;
use App\Platform\Permissions;
use App\Repository\Community\TextingCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/project/{projectUuid}/community/texting')]
class IndexController extends AbstractController
{
    use ApiControllerTrait;

    private TextingCampaignRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(TextingCampaignRepository $r, EntityManagerInterface $em)
    {
        $this->repository = $r;
        $this->em = $em;
    }

    #[Route('', name: 'console_community_texting')]
    public function index(Request $request): Response
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_TEXTING_MANAGE_DRAFTS, $project);
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $currentPage = $request->query->getInt('p', 1);

        return $this->render('console/project/community/texting/index.html.twig', [
            'project' => $project,
            'current_page' => $currentPage,
            'campaigns_drafts' => $this->repository->findAllDrafts($project),
            'campaigns_sent' => $this->repository->findAllSentPaginator($project, $currentPage),
            'items_per_page' => 30,
        ]);
    }

    #[Route('/create/{uuid}', defaults: ['uuid' => null], name: 'console_community_texting_create')]
    public function create(TranslatorInterface $translator, Request $request, ?TextingCampaign $toDuplicate = null): Response
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_TEXTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        if ($toDuplicate) {
            $campaign = $toDuplicate->duplicate();
        } else {
            $campaign = new TextingCampaign(
                $this->getProject(),
                $translator->trans('create.content', [], 'project_texting'),
            );
        }

        $this->em->persist($campaign);
        $this->em->flush();

        return $this->redirectToRoute('console_community_texting_edit', [
            'projectUuid' => $this->getProject()->getUuid(),
            'uuid' => $campaign->getUuid(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'console_community_texting_edit', methods: ['GET', 'POST'])]
    public function edit(TextingCampaign $campaign, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_TEXTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($campaign);

        // Already sent campaigns can't be edited anymore
        if ($campaign->getSentAt()) {
            throw $this->createAccessDeniedException();
        }

        $metadata = TextingCampaignMetaData::createFromCampaign($campaign);

        $form = $this->createForm(TextingCampaignMetaDataType::class, $metadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->applyMetadataUpdate($metadata);
            $this->repository->updateFilters($campaign, $metadata);

            $this->em->persist($campaign);
            $this->em->flush();

            $this->addFlash('success', 'texting.metadata_updated_success');

            return $this->redirectToRoute('console_community_texting_edit', [
                'projectUuid' => $this->getProject()->getUuid(),
                'uuid' => $campaign->getUuid(),
            ]);
        }

        return $this->render('console/project/community/texting/edit.html.twig', [
            'campaign' => $campaign,
            'project' => $this->getProject(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/delete', name: 'console_community_texting_delete', methods: ['GET'])]
    public function delete(Request $request, TextingCampaign $campaign): Response
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_TEXTING_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);
        $this->denyUnlessSameProject($campaign);

        // Deleting a sent TextingCampaign is forbidden
        if ($campaign->getSentAt()) {
            throw $this->createAccessDeniedException();
        }

        $this->em->remove($campaign);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_community_texting', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}
