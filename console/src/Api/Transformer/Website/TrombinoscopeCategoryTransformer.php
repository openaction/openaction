<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\TrombinoscopeCategory;
use App\Util\Uid;

class TrombinoscopeCategoryTransformer extends AbstractTransformer
{
    public function transform(TrombinoscopeCategory $category)
    {
        return [
            '_resource' => 'TrombinoscopeCategory',
            '_links' => [
                'self' => $this->createLink('api_website_trombinoscope_categories_view', ['id' => Uid::toBase62($category->getUuid())]),
            ],
            'id' => Uid::toBase62($category->getUuid()),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'TrombinoscopeCategory';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'self' => 'string',
            ],
            'id' => 'string',
            'name' => 'string',
            'slug' => 'string',
        ];
    }
}
