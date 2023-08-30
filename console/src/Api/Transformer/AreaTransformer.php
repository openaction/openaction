<?php

namespace App\Api\Transformer;

use App\Entity\Area;
use OpenApi\Annotations\Property;

class AreaTransformer extends AbstractTransformer
{
    public function transform(Area $area)
    {
        return [
            '_resource' => 'Area',
            'name' => $area->getName(),
            'id' => $area->getId(),
            'parentId' => $area->getParent()?->getId(),
            'type' => $area->getType(),
            'code' => $area->getCode(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'Area';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'name' => 'string',
            'id' => 'number',
            'parentId' => 'number',
            'type' => new Property(['type' => 'string', 'enum' => Area::getTypes()]),
            'code' => 'number',
        ];
    }
}
