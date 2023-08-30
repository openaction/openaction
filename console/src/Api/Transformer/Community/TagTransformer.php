<?php

namespace App\Api\Transformer\Community;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Community\Tag;

class TagTransformer extends AbstractTransformer
{
    public function transform(Tag $tag)
    {
        return [
            '_resource' => 'Tag',
            'name' => $tag->getName(),
            'slug' => $tag->getSlug(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'Tag';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'name' => 'string',
            'slug' => 'string',
        ];
    }
}
