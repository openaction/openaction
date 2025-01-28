<?php

namespace App\Controller\Shareable;

use App\Community\EmailMessageFactory;
use App\Entity\Project;
use App\Repository\Community\EmailingCampaignRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shareable/{projectId}/emailing')]
class EmailingController extends AbstractController
{
    private ProjectRepository $projectRepository;
    private EmailingCampaignRepository $repository;

    public function __construct(ProjectRepository $projectRepository, EmailingCampaignRepository $repository)
    {
        $this->projectRepository = $projectRepository;
        $this->repository = $repository;
    }

    #[Route('', name: 'shareable_emailing')]
    public function index(Request $request, string $projectId)
    {
        $project = $this->getProject($projectId);
        $currentPage = $request->query->getInt('p', 1);

        return $this->render('shareable/emailing/index.html.twig', [
            'project' => $project,
            'current_page' => $currentPage,
            'campaigns' => $this->repository->findAllSentPaginator($project, $currentPage, 10),
            'items_per_page' => 10,
        ]);
    }

    #[Route('/{id}', name: 'shareable_emailing_view')]
    public function view(EmailMessageFactory $messageFactory, string $projectId, string $id)
    {
        if (!$project = $this->projectRepository->findOneByBase62Uid($projectId)) {
            throw $this->createNotFoundException();
        }

        if (!$campaign = $this->repository->findOneByBase62Uid($id)) {
            throw $this->createNotFoundException();
        }

        if ($campaign->getProject()->getId() !== $project->getId()) {
            throw $this->createNotFoundException();
        }

        return new Response($messageFactory->createCampaignBody($campaign, true));
    }

    private function getProject(string $projectId): Project
    {
        if (!$project = $this->projectRepository->findOneByBase62Uid($projectId)) {
            throw $this->createNotFoundException();
        }

        if (!$orga = $project->getOrganization()) {
            throw $this->createNotFoundException();
        }

        if (!$orga->isSubscriptionActive()) {
            throw $this->createNotFoundException();
        }

        return $project;
    }
}
