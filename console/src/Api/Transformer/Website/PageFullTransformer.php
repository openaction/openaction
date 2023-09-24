<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\Page;
use App\Website\CustomBlockParser;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PageFullTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['categories'];
    protected array $defaultIncludes = ['categories'];

    public function __construct(
        private readonly PagePartialTransformer $partialTransformer,
        private readonly CustomBlockParser $customBlockParser,
    ) {
    }

    public function transform(Page $page)
    {
        $data = $this->partialTransformer->transform($page);
        $data['content'] = $this->customBlockParser->normalizeCustomBlocksIn($page->getContent());

        return $data;
    }

    public static function describeResourceName(): string
    {
        return 'PageFull';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'self' => 'string',
            ],
            'id' => 'string',
            'title' => 'string',
            'slug' => 'string',
            'description' => '?string',
            'image' => '?string',
            'sharer' => '?string',
            'content' => 'string',
            'categories' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/PageCategory']),
                ]),
            ],
        ];
    }

    public function includeCategories(Page $page)
    {
        return $this->partialTransformer->includeCategories($page);
    }
}
