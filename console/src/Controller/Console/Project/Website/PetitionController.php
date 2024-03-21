<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Platform\Permissions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/petitions')]
class PetitionController extends AbstractController
{
    #[Route('', name: 'console_website_petitions')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/website/petition/index.html.twig', [
            'project' => $this->getProject(),
        ]);
    }
}
