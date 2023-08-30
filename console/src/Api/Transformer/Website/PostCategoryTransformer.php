<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\PostCategory;
use App\Util\Uid;

class PostCategoryTransformer extends AbstractTransformer
{
    public function transform(PostCategory $category)
    {
        return [
            '_resource' => 'PostCategory',
            '_links' => [
                'self' => $this->createLink('api_website_posts_categories_view', ['id' => Uid::toBase62($category->getUuid())]),
            ],
            'id' => Uid::toBase62($category->getUuid()),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'PostCategory';
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
