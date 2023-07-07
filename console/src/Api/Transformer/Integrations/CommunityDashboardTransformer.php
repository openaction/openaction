<?php

namespace App\Api\Transformer\Integrations;

use App\Analytics\Model\CommunityDashboard;
use App\Api\Transformer\AbstractTransformer;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class CommunityDashboardTransformer extends AbstractTransformer
{
    public function transform(CommunityDashboard $dashboard)
    {
        return [
            '_resource' => 'CommunityDashboard',
            'totals' => $dashboard->getTotals(),
            'growth' => array_values($dashboard->getGrowth()),
            'tags' => $dashboard->getTags(),
            'countries' => $dashboard->getCountries(),
            'countriesRaw' => $dashboard->getCountriesRaw(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'CommunityDashboard';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'totals' => [
                'contacts' => 'integer',
                'members' => 'integer',
                'newsletter_subscribers' => 'integer',
                'sms_subscribers' => 'integer',
            ],
            'growth' => new Property([
                'type' => 'array',
                'items' => new Items([
                    'type' => 'object',
                    'properties' => [
                        new Property(['property' => 'period', 'type' => 'string']),
                        new Property(['property' => 'new_contacts', 'type' => 'integer']),
                        new Property(['property' => 'new_members', 'type' => 'integer']),
                    ],
                ]),
            ]),
            'tags' => [],
            'countries' => [],
            'countriesRaw' => [],
        ];
    }
}
