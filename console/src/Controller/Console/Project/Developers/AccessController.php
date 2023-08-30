<?php

namespace App\Controller\Console\Project\Developers;

use App\Controller\AbstractController;
use App\Platform\Permissions;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/developers/access')]
class AccessController extends AbstractController
{
    #[Route('', name: 'console_developers_access')]
    public function index()
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_ACCESS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/developers/api.html.twig');
    }
}
