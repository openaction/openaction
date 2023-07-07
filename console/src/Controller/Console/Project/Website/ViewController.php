<?php

namespace App\Controller\Console\Project\Website;

use App\Controller\AbstractController;
use App\Platform\Features;
use App\Proxy\DomainRouter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/website/view')]
class ViewController extends AbstractController
{
    #[Route('', name: 'console_website_view')]
    public function index(DomainRouter $domainRouter)
    {
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();
        if (!$project->isModuleEnabled(Features::MODULE_WEBSITE)) {
            throw $this->createNotFoundException();
        }

        return $this->redirect($domainRouter->generateRedirectUrl($project));
    }
}
