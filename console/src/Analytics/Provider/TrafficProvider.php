<?php

namespace App\Analytics\Provider;

use App\Analytics\Model\TrafficDashboard;
use App\Entity\Project;
use App\Repository\Analytics\Website\SessionRepository;
use App\Util\Chart;

class TrafficProvider
{
    private const BROWSER_BOTS = [
        'bingbot',
        'facebookexternalhit',
        'googlebot',
        'applebot',
        'yandex',
    ];

    private SessionRepository $sessionRepo;

    public function __construct(SessionRepository $sessionRepo)
    {
        $this->sessionRepo = $sessionRepo;
    }

    public function createDashboard(Project $project, \DateTime $startDate, int $precision): TrafficDashboard
    {
        $traffic = Chart::createEmptyDateChart($startDate, $precision, [0, 0]);

        foreach ($this->sessionRepo->findProjectTrafficSessions($project, $startDate, $precision) as $row) {
            $traffic[Chart::formatDateToPrecision(new \DateTime($row['date']), $precision)] = [
                $row['page_views'],
                $row['users'],
            ];
        }

        foreach ($traffic as $date => $values) {
            $traffic[$date] = [$date, $values[0], $values[1]];
        }

        $countries = $this->sessionRepo->findProjectTrafficCountries($project, $startDate);
        $platforms = $this->cleanPlatforms($this->sessionRepo->findProjectTrafficPlatforms($project, $startDate));
        $browsers = $this->cleanBrowsers($this->sessionRepo->findProjectTrafficBrowsers($project, $startDate));
        $utmSources = $this->sessionRepo->findProjectTrafficUtmSources($project, $startDate);
        $utmMediums = $this->sessionRepo->findProjectTrafficUtmMediums($project, $startDate);
        $utmCampaigns = $this->sessionRepo->findProjectTrafficUtmCampaigns($project, $startDate);
        $utmContents = $this->sessionRepo->findProjectTrafficUtmContents($project, $startDate);
        $events = $this->sessionRepo->findProjectEvents($project, $startDate);

        return new TrafficDashboard(
            $this->sessionRepo->findProjectTrafficTotals($project, $startDate),
            array_values($traffic),
            $this->applyLimit(12, $this->sessionRepo->findProjectTrafficPages($project, $startDate)),
            $this->applyLimit(12, $this->cleanSources($this->sessionRepo->findProjectTrafficSources($project, $startDate))),
            $this->applyLimit(12, Chart::formatAsPercentages($countries)),
            $this->applyLimit(12, $countries),
            $this->applyLimit(12, Chart::formatAsPercentages($platforms)),
            $this->applyLimit(12, $platforms),
            $this->applyLimit(12, Chart::formatAsPercentages($browsers)),
            $this->applyLimit(12, $browsers),
            $this->applyLimit(12, $utmSources),
            $this->applyLimit(12, $utmMediums),
            $this->applyLimit(12, $utmCampaigns),
            $this->applyLimit(12, $utmContents),
            $this->applyLimit(12, $events),
        );
    }

    public function createAdminDashboard(\DateTime $startDate, int $precision): array
    {
        $dashboard = [];

        // Totals
        $dashboard['totals'] = $this->sessionRepo->findAdminTrafficTotals($startDate, $precision);

        // Traffic
        $traffic = Chart::createEmptyDateChart($startDate, $precision, [0, 0]);

        foreach ($this->sessionRepo->findAdminTrafficSessions($startDate, $precision) as $row) {
            $traffic[Chart::formatDateToPrecision(new \DateTime($row['date']), $precision)] = [
                $row['page_views'],
                $row['users'],
            ];
        }

        foreach ($traffic as $date => $values) {
            $traffic[$date] = [$date, $values[0], $values[1]];
        }

        $dashboard['traffic'] = array_values($traffic);

        // Value charts
        $dashboard['projects'] = $this->applyLimit(12, $this->sessionRepo->findAdminTrafficProjects($startDate));

        return $dashboard;
    }

    private function cleanSources(iterable $data): array
    {
        $cleaned = [];

        foreach ($data as $source => $count) {
            $browser = strtolower($source);

            // Ignore invalid names
            if ('localhost' === $browser || strlen($browser) <= 2) {
                continue;
            }

            // Merge t.co in twitter.com
            if ('t.co' === $source) {
                $cleaned['twitter.com'] = ($cleaned['twitter.com'] ?? 0) + $count;
                continue;
            }

            // Remove www prefix
            if (0 === strpos($browser, 'www.')) {
                $browser = substr($browser, 4);
            }

            // Default: add to potentially already existing value due to previous operations
            $cleaned[$browser] = ($cleaned[$browser] ?? 0) + $count;
        }

        return $cleaned;
    }

    private function cleanBrowsers(iterable $data): array
    {
        $cleaned = [];

        foreach ($data as $browser => $count) {
            $browser = strtolower($browser);

            // Ignore invalid names
            if ('null' === $browser || strlen($browser) <= 2) {
                continue;
            }

            // Ignore bots
            if (\in_array($browser, self::BROWSER_BOTS, true)) {
                continue;
            }

            // Merge headlesschrome in chrome
            if ('headlesschrome' === $browser) {
                $cleaned['chrome'] = ($cleaned['chrome'] ?? 0) + $count;
                continue;
            }

            // Default: add to potentially already existing value due to previous operations
            $cleaned[$browser] = ($cleaned[$browser] ?? 0) + $count;
        }

        return $cleaned;
    }

    private function cleanPlatforms(iterable $data): array
    {
        $cleaned = [];

        foreach ($data as $browser => $count) {
            $browser = strtolower($browser);

            // Ignore invalid names
            if ('null' === $browser || strlen($browser) <= 2) {
                continue;
            }

            // Default: add to potentially already existing value due to previous operations
            $cleaned[$browser] = ($cleaned[$browser] ?? 0) + $count;
        }

        return $cleaned;
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
