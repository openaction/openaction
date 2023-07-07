<?php

namespace App\Analytics;

use App\Analytics\Model\CommunityDashboard;
use App\Analytics\Model\TrafficDashboard;
use App\Analytics\Provider\CommunityProvider;
use App\Analytics\Provider\TrafficProvider;
use App\Entity\Project;
use App\Repository\Analytics\Website\PageViewRepository;

class Analytics
{
    private PageViewRepository $pageViewRepository;
    private TrafficProvider $trafficProvider;
    private CommunityProvider $communityProvider;

    public function __construct(PageViewRepository $prr, TrafficProvider $tp, CommunityProvider $cp)
    {
        $this->pageViewRepository = $prr;
        $this->trafficProvider = $tp;
        $this->communityProvider = $cp;
    }

    public function countLiveVisitors(Project $project): int
    {
        return $this->pageViewRepository->countLiveVisitors($project);
    }

    public function createTrafficDashboard(Project $project, \DateTime $startDate, int $precision): TrafficDashboard
    {
        return $this->trafficProvider->createDashboard($project, $startDate, $precision);
    }

    public function createCommunityDashboard(Project $project, \DateTime $startDate, int $precision): CommunityDashboard
    {
        return $this->communityProvider->createDashboard($project, $startDate, $precision);
    }

    public function countAdminLiveVisitors(): int
    {
        return $this->pageViewRepository->countAllLiveVisitors();
    }

    public function createAdminTrafficDashboard(\DateTime $startDate, int $precision): array
    {
        return $this->trafficProvider->createAdminDashboard($startDate, $precision);
    }

    public function createAdminCommunityDashboard(\DateTime $startDate, int $precision): array
    {
        return $this->communityProvider->createAdminDashboard($startDate, $precision);
    }
}
