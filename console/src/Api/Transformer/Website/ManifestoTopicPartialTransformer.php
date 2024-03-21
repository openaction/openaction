<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\ManifestoTopic;
use App\Util\Uid;

class ManifestoTopicPartialTransformer extends AbstractTransformer
{
    private CdnRouter $cdnRouter;

    public function __construct(CdnRouter $cdnRouter)
    {
        $this->cdnRouter = $cdnRouter;
    }

    public function transform(ManifestoTopic $topic)
    {
        $data = [
            '_resource' => 'ManifestoTopic',
            '_links' => [
                'self' => $this->createLink('api_website_manifesto_view', ['id' => Uid::toBase62($topic->getUuid())]),
            ],
            'id' => Uid::toBase62($topic->getUuid()),
            'title' => $topic->getTitle(),
            'slug' => $topic->getSlug(),
            'description' => $topic->getDescription() ?: null,
            'image' => $topic->getImage() ? $this->cdnRouter->generateUrl($topic->getImage()) : null,
            'color' => $topic->getColor(),
            'sharer' => $topic->getImage() ? $this->cdnRouter->generateUrl($topic->getImage(), 'sharer') : null,
            'published_at' => $topic->isPublished() ? $topic->getPublishedAt()->format(\DateTime::ATOM) : null,
            'proposals' => [],
        ];

        foreach ($topic->getProposals() as $proposal) {
            $data['proposals'][] = [
                'title' => $proposal->getTitle(),
                'status' => $proposal->getStatus(),
                'statusDescription' => $proposal->getStatusDescription(),
                'statusCtaText' => $proposal->getStatusCtaText(),
                'statusCtaUrl' => $proposal->getStatusCtaUrl(),
            ];
        }

        return $data;
    }

    public static function describeResourceName(): string
    {
        return 'ManifestoTopicPartial';
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
            'color' => 'string',
            'sharer' => '?string',
            'published_at' => '?string',
        ];
    }
}
