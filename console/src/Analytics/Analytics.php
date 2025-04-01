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

    public function createTrafficDashboard(Project $project, \DateTime $startDate): TrafficDashboard
    {
        return $this->trafficProvider->createDashboard($project, $startDate);
    }

    public function createCommunityDashboard(Project $project, \DateTime $startDate): CommunityDashboard
    {
        return $this->communityProvider->createDashboard($project, $startDate);
    }

    public function countAdminLiveVisitors(): int
    {
        return $this->pageViewRepository->countAllLiveVisitors();
    }

    public function createAdminTrafficDashboard(\DateTime $startDate): array
    {
        return $this->trafficProvider->createAdminDashboard($startDate);
    }

    public function createAdminCommunityDashboard(\DateTime $startDate): array
    {
        return $this->communityProvider->createAdminDashboard($startDate);
    }
}
