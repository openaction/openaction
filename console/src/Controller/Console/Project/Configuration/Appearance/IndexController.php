<?php

namespace App\Controller\Console\Project\Configuration\Appearance;

use App\Controller\AbstractController;
use App\Platform\Permissions;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance')]
class IndexController extends AbstractController
{
    #[Route('', name: 'console_configuration_appearance')]
    public function index()
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        return $this->render('console/project/configuration/appearance/index.html.twig');
    }
}
