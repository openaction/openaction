<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class MenuItemTransformer extends AbstractTransformer
{
    public function transform(array $item)
    {
        return $item;
    }

    public static function describeResourceName(): string
    {
        return 'MenuItem';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'label' => 'string',
            'url' => 'string',
            'openNewTab' => 'boolean',
            'children' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/MenuItem']),
                ]),
            ],
        ];
    }
}
