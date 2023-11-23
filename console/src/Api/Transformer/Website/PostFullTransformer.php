<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\Post;
use App\Entity\Website\TrombinoscopePerson;
use App\Repository\Website\PostRepository;
use App\Website\CustomBlockParser;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PostFullTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['categories', 'authors', 'more'];
    protected array $defaultIncludes = ['categories', 'authors'];

    public function __construct(
        private readonly PostRepository $repository,
        private readonly PostCategoryTransformer $categoryTransformer,
        private readonly PostPartialTransformer $partialTransformer,
        private readonly TrombinoscopePersonLightTransformer $authorTransformer,
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
            'authors' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/TrombinoscopePersonLight']),
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

    public function includeAuthors(Post $post)
    {
        return $this->collection(
            $post->getAuthors()->filter(static fn (TrombinoscopePerson $p) => $p->isPublished())->toArray(),
            $this->authorTransformer,
        );
    }
}
