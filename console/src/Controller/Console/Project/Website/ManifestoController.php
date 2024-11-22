<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Platform\Permissions;
use App\Repository\Website\ManifestoTopicRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/manifesto')]
class ManifestoController extends AbstractController
{
    #[Route('', name: 'console_website_manifesto')]
    public function index(ManifestoTopicRepository $repository)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        return $this->render('console/project/website/manifesto/index.html.twig', [
            'topics' => $repository->getConsoleTopics($this->getProject()),
        ]);
    }
}
