<?php

namespace App\Controller\Api\Integrations;

use App\Analytics\Analytics;
use App\Api\Transformer\Integrations\CommunityDashboardTransformer;
use App\Api\Transformer\Integrations\TrafficDashboardTransformer;
use App\Controller\Api\AbstractApiController;
use App\Entity\Project;
use App\Platform\Permissions;
use App\Util\Chart;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Integrations')]
#[Route('/api/integrations/{uuid}/stats')]
class StatsController extends AbstractApiController
{
    private const PERIODS_CONFIG = [
        '1d' => [
            'startDate' => 'yesterday 00:00:00',
            'precision' => Chart::PRECISION_HOUR,
        ],
        '7d' => [
            'startDate' => '7 days ago 00:00:00',
            'precision' => Chart::PRECISION_DAY,
        ],
        '30d' => [
            'startDate' => '30 days ago 00:00:00',
            'precision' => Chart::PRECISION_DAY,
        ],
        '90d' => [
            'startDate' => '90 days ago 00:00:00',
            'precision' => Chart::PRECISION_DAY,
        ],
        '1y' => [
            'startDate' => '1 year ago 00:00:00',
            'precision' => Chart::PRECISION_DAY,
        ],
    ];

    /**
     * Get the traffic statistics for the given project.
     */
    #[Route('/traffic', name: 'api_integrations_stats_traffic', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return the project traffic statistics.',
        content: new OA\JsonContent(ref: '#/components/schemas/TrafficDashboard')
    )]
    public function traffic(Analytics $analytics, TrafficDashboardTransformer $transformer, Request $request, Project $project)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_ACCESS_STATS, $project);

        if (!$periodConfig = self::PERIODS_CONFIG[$request->query->get('period', '30d')] ?? null) {
            $periodConfig = self::PERIODS_CONFIG['30d'];
        }

        return $this->handleApiItem(
            $analytics->createTrafficDashboard(
                $project,
                new \DateTime($periodConfig['startDate']),
                $periodConfig['precision']
            ),
            $transformer
        );
    }

    /**
     * Get the community statistics for the given project.
     */
    #[Route('/community', name: 'api_integrations_stats_community', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return the project community statistics.',
        content: new OA\JsonContent(ref: '#/components/schemas/CommunityDashboard')
    )]
    public function community(Analytics $analytics, CommunityDashboardTransformer $transformer, Request $request, Project $project)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_ACCESS_STATS, $project);

        if (!$periodConfig = self::PERIODS_CONFIG[$request->query->get('period', '30d')] ?? null) {
            $periodConfig = self::PERIODS_CONFIG['30d'];
        }

        return $this->handleApiItem(
            $analytics->createCommunityDashboard(
                $project,
                new \DateTime($periodConfig['startDate']),
                $periodConfig['precision']
            ),
            $transformer
        );
    }
}
