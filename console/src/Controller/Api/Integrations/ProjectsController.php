<?php

namespace App\Controller\Api\Integrations;

use App\Api\Transformer\Integrations\DashboardTransformer;
use App\Controller\Api\AbstractApiController;
use App\Dashboard\DashboardBuilder;
use App\Entity\Integration\TelegramAppAuthorization;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Integrations')]
#[Route('/api/integrations/projects')]
class ProjectsController extends AbstractApiController
{
    /**
     * List current collaborator accessible projects.
     */
    #[Route('', name: 'api_integrations_projects', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return the accessible projects list.',
        content: new OA\JsonContent(ref: '#/components/schemas/Dashboard')
    )]
    public function list(DashboardBuilder $dashboardBuilder, DashboardTransformer $transformer)
    {
        /** @var TelegramAppAuthorization $authorization */
        $authorization = $this->getUser();

        return $this->handleApiItem(
            $dashboardBuilder->createOrganizationDashboard($authorization->getOrganization(), $authorization->getMember()),
            $transformer
        );
    }
}
