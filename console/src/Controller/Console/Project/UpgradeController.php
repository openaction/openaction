<?php

namespace App\Controller\Console\Project;

use App\Controller\AbstractController;
use App\Platform\Features;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}')]
class UpgradeController extends AbstractController
{
    #[Route('/upgrade', name: 'console_project_upgrade')]
    public function upgrade(Request $request)
    {
        $this->denyIfSubscriptionExpired();

        $feature = $request->query->get('feature', 'generic');
        if (!in_array($feature, Features::all(), true)) {
            $feature = 'generic';
        }

        return $this->render('console/project/upgrade.html.twig', [
            'feature' => $feature,
        ]);
    }
}
