<?php

namespace App\Admin;

use App\Analytics\Analytics;
use App\Billing\Stats\SubscriptionsStats;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DashboardStatsResolver
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly ProjectRepository $projectRepository,
        private readonly UserRepository $userRepository,
        private readonly Analytics $analytics,
        private readonly OrganizationRepository $organizationRepository,
        private readonly SubscriptionsStats $subscriptionsStats,
    ) {
    }

    public function getAdminIndexStats(): array
    {
        return $this->cache->get('admin-index-stats', fn (ItemInterface $i) => $this->computeAdminIndexStats($i));
    }

    public function refreshAdminIndexStats(): array
    {
        return $this->cache->get('admin-index-stats', fn (ItemInterface $i) => $this->computeAdminIndexStats($i), INF);
    }

    public function getAdminBillingStats(): array
    {
        return $this->cache->get('admin-billing-stats', fn (ItemInterface $i) => $this->computeAdminBillingStats($i));
    }

    public function refreshAdminBillingStats(): array
    {
        return $this->cache->get('admin-billing-stats', fn (ItemInterface $i) => $this->computeAdminBillingStats($i), INF);
    }

    private function computeAdminIndexStats(ItemInterface $item): array
    {
        $item->expiresAfter(60 * 30); // 30min (should be refreshed by CRON every 15min anyway)

        $startDate = new \DateTime('60 days ago 00:00:00');

        return [
            'organizations_count' => $this->organizationRepository->count([]),
            'users_count' => $this->userRepository->count([]),
            'projects_count' => $this->projectRepository->count([]),
            'live_visitors' => $this->analytics->countAdminLiveVisitors(),
            'traffic_dashboard' => $this->analytics->createAdminTrafficDashboard($startDate),
            'community_dashboard' => $this->analytics->createAdminCommunityDashboard($startDate),
        ];
    }

    private function computeAdminBillingStats(ItemInterface $item): array
    {
        $item->expiresAfter(60 * 30); // 30min (should be refreshed by CRON every 15min anyway)

        return [
            'active_subscriptions' => $this->organizationRepository->countActiveSubscriptions(),
            'trialing_subscriptions' => $this->organizationRepository->countTrialingSubscriptions(),
            'expired_subscriptions' => $this->organizationRepository->countExpiredSubscriptions(),
            'almost_expired_subscriptions' => $this->organizationRepository->findAlmostExpiredPayingSubscriptions(),
            'mrr' => $this->subscriptionsStats->computeMonthlyRecurringRevenue(),
        ];
    }
}
