<?php

namespace App\Controller\Console\Organization\Integrations;

use App\Controller\AbstractController;
use App\Platform\Features;
use App\Platform\Permissions;
use App\Repository\ProjectRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/console/organization/{organizationUuid}/integrations/wings')]
class WingsController extends AbstractController
{
    #[Route('', name: 'console_organization_integrations_wings')]
    public function index(ProjectRepository $projectRepository)
    {
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessFeatureInPlan(Features::FEATURE_INTEGRATION_WINGS);
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_COMMUNITY_MANAGE, $this->getOrganization());

        $projects = [];
        foreach ($projectRepository->findByOrganization($this->getOrganization()) as $project) {
            $projects[] = [
                'uuid' => $project->getUuid()->toRfc4122(),
                'name' => $project->getName(),
                'endpoint' => $this->generateUrl('webhook_wings', [
                    'uuid' => $project->getUuid()->toRfc4122(),
                    't' => $project->getApiToken(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ];
        }

        return $this->render('console/organization/integrations/wings/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
