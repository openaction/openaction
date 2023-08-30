<?php

namespace App\Controller\Console\Organization;

use App\Controller\AbstractController;
use App\Platform\Features;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}')]
class UpgradeController extends AbstractController
{
    #[Route('/upgrade', name: 'console_organization_upgrade')]
    public function upgrade(Request $request)
    {
        $this->denyIfSubscriptionExpired();

        $feature = $request->query->get('feature', 'generic');
        if (!in_array($feature, Features::all(), true)) {
            $feature = 'generic';
        }

        return $this->render('console/organization/community/upgrade.html.twig', [
            'feature' => $feature,
        ]);
    }
}
