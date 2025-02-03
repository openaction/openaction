<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\Post;
use App\Entity\Website\TrombinoscopePerson;
use App\Util\ReadTime;
use App\Util\Uid;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PostLightTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['categories', 'authors'];
    protected array $defaultIncludes = ['categories', 'authors'];

    public function __construct(
        private readonly CdnRouter $cdnRouter,
        private readonly PostCategoryTransformer $categoryTransformer,
        private readonly TrombinoscopePersonLightTransformer $authorTransformer,
    ) {
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
            'externalUrl' => $post->getExternalUrl() ?: null,
            'video' => $post->getVideo() ?: null,
            'image' => $post->getImage() ? $this->cdnRouter->generateUrl($post->getImage()) : null,
            'sharer' => $post->getImage() ? $this->cdnRouter->generateUrl($post->getImage(), 'sharer') : null,
            'read_time' => ReadTime::inMinutes($post->getContent()),
            'published_at' => $post->getPublishedAt()?->format(\DateTime::ATOM),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'PostLight';
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
            'externalUrl' => '?string',
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
            'authors' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/TrombinoscopePersonLight']),
                ]),
            ],
        ];
    }

    public function includeCategories(Post $post)
    {
        return $this->collection($post->getCategories()->toArray(), $this->categoryTransformer);
    }

    public function includeAuthors(Post $post)
    {
        return $this->collection(
            $post->getAuthors()->filter(static fn (TrombinoscopePerson $p) => $p->isPublished())->toArray(),
            $this->authorTransformer,
        );
    }
}
