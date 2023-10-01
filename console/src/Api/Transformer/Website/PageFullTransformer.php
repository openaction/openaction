<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\Page;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PageFullTransformer extends AbstractTransformer
{
    private PagePartialTransformer $partialTransformer;

    protected array $availableIncludes = ['categories'];
    protected array $defaultIncludes = ['categories'];

    public function __construct(PagePartialTransformer $partialTransformer)
    {
        $this->partialTransformer = $partialTransformer;
    }

    public function transform(Page $page)
    {
        $data = $this->partialTransformer->transform($page);
        $data['content'] = $page->getContent();

        $data['children']['data'] = [];
        foreach ($page->getChildren() as $child) {
            $data['children']['data'][] = $this->partialTransformer->transform($child);
        }

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
            'children' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/Page']),
                ]),
            ],
        ];
    }

    public function includeCategories(Page $page)
    {
        return $this->partialTransformer->includeCategories($page);
    }
}
