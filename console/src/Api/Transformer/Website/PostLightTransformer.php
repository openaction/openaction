<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\Post;
use App\Util\ReadTime;
use App\Util\Uid;

class PostLightTransformer extends AbstractTransformer
{
    public function __construct(private readonly CdnRouter $cdnRouter)
    {
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
        ];
    }
}
