<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\TrombinoscopePerson;
use App\Repository\Website\TrombinoscopePersonRepository;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class TrombinoscopePersonFullTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['previous', 'next'];

    private TrombinoscopePersonRepository $repository;
    private TrombinoscopePersonPartialTransformer $partialTransformer;

    public function __construct(TrombinoscopePersonRepository $repository, TrombinoscopePersonPartialTransformer $partialTransformer)
    {
        $this->repository = $repository;
        $this->partialTransformer = $partialTransformer;
    }

    public function transform(TrombinoscopePerson $person)
    {
        $data = $this->partialTransformer->transform($person);
        $data['content'] = $person->getContent();

        return $data;
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
            'title' => 'string',
            'quote' => '?string',
            'slug' => 'string',
            'description' => '?string',
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
            'previous' => new Property(['ref' => '#/components/schemas/TrombinoscopePersonPartial']),
            'next' => new Property(['ref' => '#/components/schemas/TrombinoscopePersonPartial']),
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
}
