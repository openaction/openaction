<?php

namespace App\Analytics\Provider;

use App\Analytics\Model\CommunityDashboard;
use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Entity\Project;
use App\Repository\Community\ContactRepository;
use App\Util\Chart;

class CommunityProvider
{
    private MeilisearchInterface $meilisearch;
    private ContactRepository $contactRepository;

    public function __construct(MeilisearchInterface $meilisearch, ContactRepository $contactRepository)
    {
        $this->meilisearch = $meilisearch;
        $this->contactRepository = $contactRepository;
    }

    public function createDashboard(Project $project, \DateTime $startDate): CommunityDashboard
    {
        $index = $project->getOrganization()->getCrmIndexName();
        $baseFilter = ["projects = '".$project->getUuid()->toRfc4122()."'"];

        /*
         * Global stats
         */

        $stats = $this->meilisearch->findFacetStats(
            index: $index,
            facets: [
                'tags_names',
                'address_country',
                'settings_receive_newsletters',
                'settings_receive_sms',
                'status',
            ],
            searchParams: ['filter' => $baseFilter],
        );

        // Totals
        $totals = [
            'contacts' => $stats['total'],
            'members' => $stats['status']['m'],
            'newsletter_subscribers' => $stats['settings_receive_newsletters']['true'],
            'sms_subscribers' => $stats['settings_receive_sms']['true'],
        ];

        // Tags
        $tags = $stats['tags_names'];
        arsort($tags);

        // Countries
        $countries = [];
        foreach ($stats['address_country'] as $code => $value) {
            $countries[strtolower($code)] = $value;
        }
        arsort($countries);

        /*
         * Growth
         */

        $contactsGrowth = $this->meilisearch->findFacetStats(
            index: $index,
            facets: ['created_at_int'],
            searchParams: [
                'filter' => array_merge($baseFilter, [
                    'created_at_int >= '.$startDate->format('Ymd'),
                ]),
            ],
        )['created_at_int'] ?? [];

        $membersGrowth = $this->meilisearch->findFacetStats(
            index: $index,
            facets: ['created_at_int'],
            searchParams: [
                'filter' => array_merge($baseFilter, [
                    'created_at_int >= '.$startDate->format('Ymd'),
                    "status = 'm'",
                ]),
            ],
        )['created_at_int'] ?? [];

        $growth = Chart::createEmptyDateChart($startDate, [0, 0]);
        foreach ($contactsGrowth as $day => $value) {
            $date = \DateTime::createFromFormat('Ymd', $day);
            $growth[$date->format('Y-m-d')] = [$value, $membersGrowth[$day] ?? 0];
        }

        foreach ($growth as $date => $values) {
            $growth[$date] = [$date, $values[0], $values[1]];
        }

        return new CommunityDashboard(
            $totals,
            array_values($growth),
            $this->applyLimit(12, $tags),
            $this->applyLimit(12, Chart::formatAsPercentages($countries)),
            $this->applyLimit(12, $countries),
        );
    }

    public function createAdminDashboard(\DateTime $startDate): array
    {
        $dashboard = [];

        // Totals
        $dashboard['totals'] = $this->contactRepository->findAdminCommunityTotals();

        // Growth
        $growth = Chart::createEmptyDateChart($startDate, [0, 0]);

        foreach ($this->contactRepository->findAdminCommunityGrowth($startDate) as $row) {
            $growth[(new \DateTime($row['period']))->format('Y-m-d')] = [
                $row['new_contacts'],
                $row['new_members'],
            ];
        }

        foreach ($growth as $date => $values) {
            $growth[$date] = [$date, $values[0], $values[1]];
        }

        $dashboard['growth'] = array_values($growth);

        // Value charts
        $dashboard['organizations'] = $this->applyLimit(12, $this->contactRepository->findAdminCommunityOrganizations());

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
