<?php

namespace App\Api\Transformer\Integrations;

use App\Analytics\Model\TrafficDashboard;
use App\Api\Transformer\AbstractTransformer;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class TrafficDashboardTransformer extends AbstractTransformer
{
    public function transform(TrafficDashboard $dashboard)
    {
        return [
            '_resource' => 'TrafficDashboard',
            'totals' => $dashboard->getTotals(),
            'traffic' => array_values($dashboard->getTraffic()),
            'pages' => $dashboard->getPages(),
            'sources' => $dashboard->getSources(),
            'countries' => $dashboard->getCountries(),
            'countriesRaw' => $dashboard->getCountriesRaw(),
            'platforms' => $dashboard->getPlatforms(),
            'platformsRaw' => $dashboard->getPlatformsRaw(),
            'browsers' => $dashboard->getBrowsers(),
            'browsersRaw' => $dashboard->getBrowsersRaw(),
            'utmSources' => $dashboard->getUtmSources(),
            'utmMedium' => $dashboard->getUtmMedium(),
            'utmCampaign' => $dashboard->getUtmCampaign(),
            'utmContent' => $dashboard->getUtmContent(),
            'events' => $dashboard->getEvents(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'TrafficDashboard';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'totals' => [
                'users' => 'integer',
                'page_views' => 'integer',
            ],
            'traffic' => new Property([
                'type' => 'array',
                'items' => new Items(['type' => 'string']),
            ]),
            'pages' => [],
            'sources' => [],
            'countries' => [],
            'countriesRaw' => [],
            'platforms' => [],
            'platformsRaw' => [],
            'browsers' => [],
            'browsersRaw' => [],
            'utmSources' => [],
            'utmMedium' => [],
            'utmCampaign' => [],
            'utmContent' => [],
            'events' => [],
        ];
    }
}
