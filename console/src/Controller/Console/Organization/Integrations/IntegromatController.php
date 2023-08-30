<?php

namespace App\Controller\Console\Organization\Integrations;

use App\Controller\AbstractController;
use App\Platform\Features;
use App\Platform\Permissions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/integrations/integromat')]
class IntegromatController extends AbstractController
{
    #[Route('', name: 'console_organization_integrations_integromat')]
    public function index(Request $request)
    {
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_INTEGROMAT);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());

        return $this->render('console/organization/integrations/integromat/index.html.twig', [
            'section' => $request->query->get('section'),
            'integration' => $request->query->get('integration'),
        ]);
    }
}
