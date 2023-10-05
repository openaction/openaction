<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\Post;
use App\Repository\Website\PostRepository;
use App\Website\CustomBlockParser;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PostFullTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['categories', 'more'];
    protected array $defaultIncludes = ['categories'];

    public function __construct(
        private readonly PostRepository $repository,
        private readonly PostCategoryTransformer $categoryTransformer,
        private readonly PostPartialTransformer $partialTransformer,
        private readonly CustomBlockParser $customBlockParser,
    ) {
    }

    public function transform(Post $post)
    {
        $data = $this->partialTransformer->transform($post);
        $data['content'] = $this->customBlockParser->normalizeCustomBlocksIn($post->getContent());

        return $data;
    }

    public static function describeResourceName(): string
    {
        return 'PostFull';
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
            'content' => 'string',
            'categories' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/PostCategory']),
                ]),
            ],
            'more' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/PostPartial']),
                ]),
            ],
        ];
    }

    public function includeCategories(Post $post)
    {
        return $this->collection($post->getCategories()->toArray(), $this->categoryTransformer);
    }

    public function includeMore(Post $post)
    {
        return $this->collection($this->repository->getMorePosts($post), $this->partialTransformer);
    }
}
