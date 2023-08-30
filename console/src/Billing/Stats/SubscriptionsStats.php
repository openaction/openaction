<?php

namespace App\Billing\Stats;

use App\Repository\OrganizationRepository;

class SubscriptionsStats
{
    private OrganizationRepository $repository;

    public function __construct(OrganizationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function computeMonthlyRecurringRevenue(): array
    {
        $mrr = [];

        $date = new \DateTimeImmutable();
        $endDate = new \DateTimeImmutable('+2 years');
        while ($date < $endDate) {
            $mrr[$date->format('Y-m')] = 0;
            $date = $date->add(\DateInterval::createFromDateString('1 month'));
        }

        foreach ($this->repository->findBy(['subscriptionTrialing' => false]) as $orga) {
            if (!$pricePerMonth = $orga->getBillingPricePerMonth()) {
                continue;
            }

            $date = new \DateTimeImmutable();
            while ($date < $orga->getSubscriptionCurrentPeriodEnd()) {
                if (!isset($mrr[$date->format('Y-m')])) {
                    break;
                }

                $mrr[$date->format('Y-m')] += round($pricePerMonth / 100, 2);
                $date = $date->add(\DateInterval::createFromDateString('1 month'));
            }
        }

        $chart = [];
        $nextYearTotal = 0;
        $i = 0;

        foreach ($mrr as $month => $revenue) {
            ++$i;

            $chart[] = [$month, $revenue];

            if ($i <= 12) {
                $nextYearTotal += $revenue;
            }
        }

        return [
            'chart' => $chart,
            'next_year_average' => $nextYearTotal / 12,
        ];
    }
}
