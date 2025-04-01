<?php

namespace App\Controller\Console\Project;

use App\Analytics\Analytics;
use App\Controller\AbstractController;
use App\Platform\Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/stats')]
class StatsController extends AbstractController
{
    private const PERIODS_CONFIG = [
        '7d' => '7 days ago 00:00:00',
        '30d' => '30 days ago 00:00:00',
        '90d' => '90 days ago 00:00:00',
        '1y' => '1 year ago 00:00:00',
    ];

    #[Route('/traffic', name: 'console_stats_traffic')]
    public function traffic(Analytics $analytics, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_ACCESS_STATS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $period = $request->query->get('period', '30d');
        if (!$startDate = self::PERIODS_CONFIG[$request->query->get('period', '30d')] ?? null) {
            $period = '30d';
            $startDate = self::PERIODS_CONFIG['30d'];
        }

        return $this->render('console/project/stats/traffic.html.twig', [
            'periods' => array_keys(self::PERIODS_CONFIG),
            'current_period' => $period,
            'dashboard' => $analytics->createTrafficDashboard($this->getProject(), new \DateTime($startDate)),
        ]);
    }

    #[Route('/traffic/live', name: 'console_stats_traffic_live')]
    public function trafficLive(Analytics $analytics)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_ACCESS_STATS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return new JsonResponse(['count' => $analytics->countLiveVisitors($this->getProject())]);
    }

    #[Route('/community', name: 'console_stats_community')]
    public function community(Analytics $analytics, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::COMMUNITY_ACCESS_STATS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $period = $request->query->get('period', '30d');
        if (!$startDate = self::PERIODS_CONFIG[$request->query->get('period', '30d')] ?? null) {
            $period = '30d';
            $startDate = self::PERIODS_CONFIG['30d'];
        }

        return $this->render('console/project/stats/community.html.twig', [
            'periods' => array_keys(self::PERIODS_CONFIG),
            'current_period' => $period,
            'dashboard' => $analytics->createCommunityDashboard($this->getProject(), new \DateTime($startDate)),
        ]);
    }
}
