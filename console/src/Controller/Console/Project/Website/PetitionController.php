<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Entity\Website\LocalizedPetition;
use App\Entity\Website\Petition;
use App\Platform\Permissions;
use App\Repository\Website\PetitionCategoryRepository;
use App\Repository\Website\PetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/petitions')]
class PetitionController extends AbstractController
{
    public function __construct(
        private readonly PetitionRepository $repository,
        private readonly PetitionCategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em,
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

    #[Route('/create', name: 'console_website_petition_create')]
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();

        // Create empty petition and a first localization using current locale
        $petition = new Petition($project, 'petition');
        $localized = new LocalizedPetition($petition, $request->getLocale(), '');

        $this->em->persist($petition);
        $this->em->persist($localized);
        $this->em->flush();

        return $this->redirectToRoute('console_website_petition_localized_edit', [
            'projectUuid' => $project->getUuid(),
            'uuid' => $localized->getUuid(),
        ]);
    }
}
