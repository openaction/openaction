<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\PageCategory;
use App\Util\Uid;

class PageCategoryTransformer extends AbstractTransformer
{
    public function transform(PageCategory $category)
    {
        return [
            '_resource' => 'PageCategory',
            '_links' => [
                'self' => $this->createLink('api_website_pages_categories_view', ['id' => Uid::toBase62($category->getUuid())]),
            ],
            'id' => Uid::toBase62($category->getUuid()),
            'projectId' =>  Uid::toBase62($category->getProject()->getUuid()),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'PageCategory';
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
