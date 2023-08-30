<?php

namespace App\Controller\Console\Organization\Integrations;

use App\Controller\AbstractController;
use App\Platform\Permissions;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/integrations')]
class IndexController extends AbstractController
{
    #[Route('', name: 'console_organization_integrations')]
    public function index()
    {
        $this->denyIfSubscriptionExpired();
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());

        return $this->render('console/organization/integrations/index.html.twig');
    }
}
