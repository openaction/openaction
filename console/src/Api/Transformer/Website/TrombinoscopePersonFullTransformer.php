<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\Event;
use App\Entity\Website\Post;
use App\Entity\Website\TrombinoscopePerson;
use App\Repository\Website\TrombinoscopePersonRepository;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class TrombinoscopePersonFullTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['previous', 'next', 'posts', 'events', 'categories'];
    protected array $defaultIncludes = ['previous', 'next', 'posts', 'events', 'categories'];

    public function __construct(
        private readonly TrombinoscopePersonRepository $repository,
        private readonly TrombinoscopeCategoryTransformer $categoryTransformer,
        private readonly TrombinoscopePersonPartialTransformer $partialTransformer,
        private readonly PostLightTransformer $postLightTransformer,
        private readonly EventLightTransformer $eventLightTransformer,
    ) {
    }

    public function transform(TrombinoscopePerson $person)
    {
        return $this->partialTransformer->transform($person);
    }

    public static function describeResourceName(): string
    {
        return 'TrombinoscopePersonFull';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'self' => 'string',
            ],
            'id' => 'string',
            'previous' => new Property(['ref' => '#/components/schemas/TrombinoscopePersonPartial']),
            'next' => new Property(['ref' => '#/components/schemas/TrombinoscopePersonPartial']),
            'posts' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/Post']),
                ]),
            ],
            'events' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/Event']),
                ]),
            ],
        ];
    }

    public function includePrevious(TrombinoscopePerson $person)
    {
        if ($previous = $this->repository->getPersonsNextTo($person, 'previous')) {
            return $this->item($previous, $this->partialTransformer);
        }

        return null;
    }

    public function includeNext(TrombinoscopePerson $person)
    {
        if ($next = $this->repository->getPersonsNextTo($person, 'next')) {
            return $this->item($next, $this->partialTransformer);
        }

        return null;
    }

    public function includePosts(TrombinoscopePerson $person)
    {
        return $this->collection(
            $person->getPosts()->filter(static fn (Post $p) => $p->isPublished() && !$p->isOnlyForMembers())->slice(0, 6),
            $this->postLightTransformer,
        );
    }

    public function includeEvents(TrombinoscopePerson $person)
    {
        return $this->collection(
            $person->getEvents()->filter(static fn (Event $e) => $e->isPublished() && !$e->isOnlyForMembers())->slice(0, 6),
            $this->eventLightTransformer,
        );
    }

    public function includeCategories(TrombinoscopePerson $person)
    {
        return $this->collection($person->getCategories()->toArray(), $this->categoryTransformer);
    }
}
