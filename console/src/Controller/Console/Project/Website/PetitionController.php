<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Platform\Permissions;
use App\Repository\Website\PetitionCategoryRepository;
use App\Repository\Website\PetitionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/petitions')]
class PetitionController extends AbstractController
{
    public function __construct(
        private readonly PetitionRepository $repository,
        private readonly PetitionCategoryRepository $categoryRepository,
    ) {
    }

    #[Route('', name: 'console_website_petitions')]
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $project = $this->getProject();
        $currentCategory = $request->query->getInt('c');
        $currentPage = $request->query->getInt('p', 1);
        $currentQuery = $request->query->get('q');

        return $this->render('console/project/website/petition/index.html.twig', [
            'petitions' => $this->repository->getConsolePaginator($project, $currentQuery, $currentCategory, $currentPage),
            'categories' => $this->categoryRepository->getProjectCategories($project),
            'current_category' => $currentCategory,
            'current_page' => $currentPage,
            'current_query' => $currentQuery,
            'project' => $project,
        ]);
    }
}

