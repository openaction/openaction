<?php

namespace App\Controller\Console\Project;

use App\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}')]
class HomeController extends AbstractController
{
    #[Route('/start', name: 'console_project_home_start')]
    public function start()
    {
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/home/start.html.twig');
    }
}
