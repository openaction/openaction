<?php

namespace App\Controller\Console\Project\Configuration\ContentImport;

use App\Controller\AbstractController;
use App\Platform\Permissions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/content-import')]
class IndexController extends AbstractController
{
    #[Route('', name: 'console_project_configuration_content_import')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        return $this->render('console/project/configuration/content_import/index.html.twig');
    }
}
