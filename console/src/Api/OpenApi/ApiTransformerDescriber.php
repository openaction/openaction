<?php

namespace App\Api\OpenApi;

use App\Api\Transformer\AbstractTransformer;
use Nelmio\ApiDocBundle\Model\Model;
use Nelmio\ApiDocBundle\ModelDescriber\ModelDescriberInterface;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;

class ApiTransformerDescriber implements ModelDescriberInterface
{
    public function supports(Model $model): bool
    {
        return in_array(AbstractTransformer::class, class_parents($model->getType()->getClassName()), true);
    }

    public function describe(Model $model, Schema $schema)
    {
        $schema->title = call_user_func([$model->getType()->getClassName(), 'describeResourceName']);

        $properties = call_user_func([$model->getType()->getClassName(), 'describeResourceSchema']);
        $schema->properties = $this->createPropertiesTree($properties);
    }

    private function createPropertiesTree(array $properties): array
    {
        $tree = [];
        foreach ($properties as $propertyName => $type) {
            if ($type instanceof Property) {
                $type->property = $propertyName;
                $tree[] = $type;

                continue;
            }

            if (is_array($type)) {
                $children = $this->createPropertiesTree($type);
                $tree[] = new Property(['property' => $propertyName, 'type' => 'object', 'properties' => $children]);

                continue;
            }

            $nullable = false;
            if (str_starts_with($type, '?')) {
                $type = substr($type, 1);
                $nullable = true;
            }

            $tree[] = new Property(['property' => $propertyName, 'type' => $type, 'nullable' => $nullable]);
        }

        return $tree;
    }
}
