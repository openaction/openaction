<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\ManifestoTopic;
use App\Repository\Website\ManifestoTopicRepository;
use App\Website\CustomBlockParser;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class ManifestoTopicFullTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['previous', 'next'];

    public function __construct(
        private readonly ManifestoTopicRepository $repository,
        private readonly ManifestoTopicPartialTransformer $partialTransformer,
        private readonly CustomBlockParser $customBlockParser,
    ) {
    }

    public function transform(ManifestoTopic $topic)
    {
        $data = $this->partialTransformer->transform($topic);
        $data['proposals'] = [];

        foreach ($topic->getProposals() as $proposal) {
            $data['proposals'][] = [
                'title' => $proposal->getTitle(),
                'content' => $this->customBlockParser->normalizeCustomBlocksIn($proposal->getContent()),
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
        return 'ManifestoTopicFull';
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
            'proposals' => new Property([
                'type' => 'array',
                'items' => new Items([
                    'type' => 'object',
                    'properties' => [
                        new Property(['property' => 'title', 'type' => 'string']),
                        new Property(['property' => 'content', 'type' => 'string']),
                        new Property(['property' => 'status', 'type' => 'string', 'nullable' => true]),
                        new Property(['property' => 'statusDescription', 'type' => 'string', 'nullable' => true]),
                        new Property(['property' => 'statusCtaText', 'type' => 'string', 'nullable' => true]),
                        new Property(['property' => 'statusCtaUrl', 'type' => 'string', 'nullable' => true]),
                    ],
                ]),
            ]),
            'previous' => new Property(['ref' => '#/components/schemas/ManifestoTopicPartial']),
            'next' => new Property(['ref' => '#/components/schemas/ManifestoTopicPartial']),
        ];
    }

    public function includePrevious(ManifestoTopic $topic)
    {
        if ($previous = $this->repository->getTopicNextTo($topic, 'previous')) {
            return $this->item($previous, $this->partialTransformer);
        }

        return null;
    }

    public function includeNext(ManifestoTopic $topic)
    {
        if ($next = $this->repository->getTopicNextTo($topic, 'next')) {
            return $this->item($next, $this->partialTransformer);
        }

        return null;
    }
}
