<?php

namespace App\Analytics\Provider;

use App\Analytics\Model\CommunityDashboard;
use App\Community\ContactViewBuilder;
use App\Entity\Project;
use App\Repository\Analytics\Community\ContactCreationRepository;
use App\Repository\Community\ContactRepository;
use App\Util\Chart;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class CommunityProvider
{
    private EntityManagerInterface $em;
    private ContactViewBuilder $viewBuilder;

    public function __construct(EntityManagerInterface $em, ContactViewBuilder $viewBuilder)
    {
        $this->em = $em;
        $this->viewBuilder = $viewBuilder;
    }

    public function createDashboard(Project $project, \DateTime $startDate, int $precision): CommunityDashboard
    {
        $query = $this->viewBuilder->inProject($project)->createdBetween($startDate, new \DateTime())->toIdsQuery();
        $sql = $query->getSQL();
        $params = array_map(fn (Parameter $p) => $p->getValue(), $query->getParameters()->toArray());

        /*
         * Growth
         */

        $growthData = $this->em->getConnection()->executeQuery(
            sql: "
                SELECT
                   TO_TIMESTAMP(FLOOR((EXTRACT('epoch' from created_at) / $precision)) * $precision) as period,
                   COUNT(*) AS new_contacts,
                   COUNT(CASE WHEN account_password IS NOT NULL THEN 1 END) AS new_members
                FROM community_contacts
                WHERE id IN ($sql)
                GROUP BY period
                ORDER BY period
            ",
            params: $params,
        );

        $growth = Chart::createEmptyDateChart($startDate, $precision, [0, 0]);
        foreach ($growthData->fetchAllAssociative() as $row) {
            $growth[Chart::formatDateToPrecision(new \DateTime($row['period']), $precision)] = [
                $row['new_contacts'],
                $row['new_members'],
            ];
        }

        foreach ($growth as $date => $values) {
            $growth[$date] = [$date, $values[0], $values[1]];
        }

        /*
         * Countries
         */

        $countriesData = $this->em->getConnection()->executeQuery(
            sql: "
                SELECT a.code, COUNT(*) AS value
                FROM community_contacts c
                LEFT JOIN areas a on c.address_country_id = a.id
                WHERE c.id IN ($sql) AND a.code IS NOT NULL
                GROUP BY a.code
                ORDER BY value DESC
            ",
            params: $params,
        );

        dd($params);

        echo "
                SELECT a.code, COUNT(*) AS value
                FROM community_contacts c
                LEFT JOIN areas a on c.address_country_id = a.id
                WHERE c.id IN ($sql) AND a.code IS NOT NULL
                GROUP BY a.code
                ORDER BY value DESC
            ";
        exit;

        dd($countriesData->fetchAllAssociative());

        $countries = [];
        foreach ($countriesData->fetchAllAssociative() as $row) {
            $countries[$row['code']] = $row['value'];
        }

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
