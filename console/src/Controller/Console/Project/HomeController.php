<?php

namespace App\Controller\Console\Project;

use App\Controller\AbstractController;
use App\Platform\Features;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}')]
class HomeController extends AbstractController
{
    #[Route('/start', name: 'console_project_home_start')]
    public function start()
    {
        $this->denyIfSubscriptionExpired();

        if ($this->getProject()->getTools() === [Features::TOOL_COMMUNITY_PRINTING]) {
            return $this->redirectToRoute('console_community_printing', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/home/start.html.twig');
    }
}
