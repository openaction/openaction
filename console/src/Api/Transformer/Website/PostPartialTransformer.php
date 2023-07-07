<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\Post;
use App\Util\Uid;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PostPartialTransformer extends AbstractTransformer
{
    private PostCategoryTransformer $categoryTransformer;
    private CdnRouter $cdnRouter;

    protected array $availableIncludes = ['categories'];
    protected array $defaultIncludes = ['categories'];

    public function __construct(PostCategoryTransformer $categoryTransformer, CdnRouter $cdnRouter)
    {
        $this->categoryTransformer = $categoryTransformer;
        $this->cdnRouter = $cdnRouter;
    }

    public function transform(Post $post)
    {
        return [
            '_resource' => 'Post',
            '_links' => [
                'self' => (
                    $post->isOnlyForMembers()
                        ? $this->createLink('api_area_posts_view', ['id' => Uid::toBase62($post->getUuid())])
                        : $this->createLink('api_website_posts_view', ['id' => Uid::toBase62($post->getUuid())])
                ),
            ],
            'id' => Uid::toBase62($post->getUuid()),
            'title' => $post->getTitle(),
            'quote' => $post->getQuote(),
            'slug' => $post->getSlug(),
            'description' => $post->getDescription() ?: null,
            'video' => $post->getVideo() ?: null,
            'image' => $post->getImage() ? $this->cdnRouter->generateUrl($post->getImage()) : null,
            'sharer' => $post->getImage() ? $this->cdnRouter->generateUrl($post->getImage(), 'sharer') : null,
            'published_at' => $post->getPublishedAt()?->format(\DateTime::ATOM),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'PostPartial';
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
            'quote' => '?string',
            'slug' => 'string',
            'description' => '?string',
            'video' => '?string',
            'image' => '?string',
            'sharer' => '?string',
            'published_at' => '?string',
            'categories' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/PostCategory']),
                ]),
            ],
        ];
    }

    public function includeCategories(Post $post)
    {
        return $this->collection($post->getCategories()->toArray(), $this->categoryTransformer);
    }
}
