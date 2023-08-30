<?php

namespace App\Api\Transformer\Integrations;

use App\Api\Transformer\AbstractTransformer;
use App\Api\Transformer\AreaTransformer;
use App\Dashboard\Model\OrganizationDashboard;
use App\Dashboard\Model\OrganizationDashboardItem;
use App\Platform\Features;
use Doctrine\Common\Collections\Collection;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class DashboardTransformer extends AbstractTransformer
{
    private AreaTransformer $areaTransformer;

    public function __construct(AreaTransformer $areaTransformer)
    {
        $this->areaTransformer = $areaTransformer;
    }

    public function transform(OrganizationDashboard $dashboard)
    {
        return [
            '_resource' => 'Dashboard',
            'organization' => $dashboard->getOrganization()->getName(),
            'globalProjects' => array_map([$this, 'transformItem'], $dashboard->getGlobalProjects()),
            'localProjects' => array_map([$this, 'transformItem'], $dashboard->getLocalProjects()),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'Dashboard';
    }

    public static function describeResourceSchema(): array
    {
        $projectsSchema = new Property([
            'type' => 'array',
            'items' => new Items([
                'type' => 'object',
                'properties' => [
                    new Property(['property' => '_resource', 'type' => 'string']),
                    new Property([
                        'property' => '_links',
                        'type' => 'object',
                        'properties' => [
                            new Property(['property' => 'stats_traffic', 'type' => 'string']),
                            new Property(['property' => 'stats_community', 'type' => 'string']),
                        ],
                    ]),
                    new Property(['property' => 'id', 'type' => 'string']),
                    new Property(['property' => 'name', 'type' => 'string']),
                    new Property([
                        'property' => 'areas',
                        'type' => 'array',
                        'items' => new Items(['ref' => '#/components/schemas/Area']),
                    ]),
                    new Property([
                        'property' => 'tools',
                        'type' => 'array',
                        'items' => new Items(['type' => 'string', 'enum' => Features::allTools()]),
                    ]),
                    new Property(['property' => 'contacts', 'type' => 'integer']),
                    new Property(['property' => 'members', 'type' => 'integer']),
                ],
            ]),
        ]);

        return [
            '_resource' => 'string',
            'organization' => 'string',
            'globalProjects' => clone $projectsSchema,
            'localProjects' => clone $projectsSchema,
        ];
    }

    private function transformItem(OrganizationDashboardItem $item): array
    {
        return [
            '_resource' => 'DashboardItem',
            '_links' => [
                'stats_traffic' => $this->createLink(
                    'api_integrations_stats_traffic',
                    ['uuid' => $item->getProject()->getUuid()->toRfc4122()]
                ),
                'stats_community' => $this->createLink(
                    'api_integrations_stats_community',
                    ['uuid' => $item->getProject()->getUuid()->toRfc4122()]
                ),
            ],
            'id' => $item->getProject()->getUuid()->toRfc4122(),
            'name' => $item->getProject()->getName(),
            'areas' => $this->transformAreas($item->getProject()->getAreas()),
            'tools' => $item->getProject()->getTools(),
            'contacts' => $item->getContacts(),
            'members' => $item->getMembers(),
        ];
    }

    private function transformAreas(Collection $areas): ?array
    {
        if (0 === $areas->count()) {
            return null;
        }

        $transformed = [];
        foreach ($areas as $area) {
            $transformed[] = $this->areaTransformer->transform($area);
        }

        return $transformed;
    }
}
