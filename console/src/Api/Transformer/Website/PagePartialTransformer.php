<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\Page;
use App\Util\ReadTime;
use App\Util\Uid;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PagePartialTransformer extends AbstractTransformer
{
    private PageCategoryTransformer $categoryTransformer;
    private CdnRouter $cdnRouter;

    protected array $availableIncludes = ['categories'];
    protected array $defaultIncludes = ['categories'];

    public function __construct(PageCategoryTransformer $categoryTransformer, CdnRouter $cdnRouter)
    {
        $this->categoryTransformer = $categoryTransformer;
        $this->cdnRouter = $cdnRouter;
    }

    public function transform(Page $page)
    {
        return [
            '_resource' => 'Page',
            '_links' => [
                'self' => (
                    $page->isOnlyForMembers()
                        ? $this->createLink('api_area_pages_view', ['id' => Uid::toBase62($page->getUuid())])
                        : $this->createLink('api_website_pages_view', ['id' => Uid::toBase62($page->getUuid())])
                ),
            ],
            'id' => Uid::toBase62($page->getUuid()),
            'title' => $page->getTitle(),
            'slug' => $page->getSlug(),
            'description' => $page->getDescription() ?: null,
            'image' => $page->getImage() ? $this->cdnRouter->generateUrl($page->getImage()) : null,
            'sharer' => $page->getImage() ? $this->cdnRouter->generateUrl($page->getImage(), 'sharer') : null,
            'read_time' => ReadTime::inMinutes($page->getContent()),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'PagePartial';
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
        return $this->collection($page->getCategories()->toArray(), $this->categoryTransformer);
    }
}
