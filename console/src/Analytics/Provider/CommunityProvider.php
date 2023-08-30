<?php

namespace App\Analytics\Provider;

use App\Analytics\Model\CommunityDashboard;
use App\Entity\Project;
use App\Repository\Analytics\Community\ContactCreationRepository;
use App\Util\Chart;

class CommunityProvider
{
    private ContactCreationRepository $repository;

    public function __construct(ContactCreationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createDashboard(Project $project, \DateTime $startDate, int $precision): CommunityDashboard
    {
        $growth = Chart::createEmptyDateChart($startDate, $precision, [0, 0]);

        foreach ($this->repository->findProjectCommunityGrowth($project, $startDate, $precision) as $row) {
            $growth[Chart::formatDateToPrecision(new \DateTime($row['period']), $precision)] = [
                $row['new_contacts'],
                $row['new_members'],
            ];
        }

        foreach ($growth as $date => $values) {
            $growth[$date] = [$date, $values[0], $values[1]];
        }

        $countries = iterator_to_array($this->repository->findProjectCommunityCountries($project));

        return new CommunityDashboard(
            $this->repository->findProjectCommunityTotals($project),
            array_values($growth),
            $this->applyLimit(12, $this->repository->findProjectCommunityTags($project)),
            $this->applyLimit(12, Chart::formatAsPercentages($countries)),
            $this->applyLimit(12, $countries),
        );
    }

    public function createAdminDashboard(\DateTime $startDate, int $precision): array
    {
        $dashboard = [];

        // Totals
        $dashboard['totals'] = $this->repository->findAdminCommunityTotals($startDate);

        // Growth
        $growth = Chart::createEmptyDateChart($startDate, $precision, [0, 0]);

        foreach ($this->repository->findAdminCommunityGrowth($startDate, $precision) as $row) {
            $growth[Chart::formatDateToPrecision(new \DateTime($row['period']), $precision)] = [
                $row['new_contacts'],
                $row['new_members'],
            ];
        }

        foreach ($growth as $date => $values) {
            $growth[$date] = [$date, $values[0], $values[1]];
        }

        $dashboard['growth'] = array_values($growth);

        // Value charts
        $dashboard['organizations'] = $this->applyLimit(12, $this->repository->findAdminCommunityOrganizations());

        // Percentage charts
        $dashboard['countries'] = $this->applyLimit(12, $this->repository->findAdminCommunityCountries());

        return $dashboard;
    }

    private function applyLimit(int $limit, iterable $data): array
    {
        $limited = [];
        foreach ($data as $key => $value) {
            $limited[$key] = $value;

            if (count($limited) >= $limit) {
                break;
            }
        }

        return $limited;
    }
}
