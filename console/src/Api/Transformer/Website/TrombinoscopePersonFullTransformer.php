<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\TrombinoscopePerson;
use App\Repository\Website\TrombinoscopePersonRepository;
use App\Website\CustomBlockParser;
use OpenApi\Annotations\Property;

class TrombinoscopePersonFullTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['previous', 'next'];

    public function __construct(
        private readonly TrombinoscopePersonRepository $repository,
        private readonly TrombinoscopePersonPartialTransformer $partialTransformer,
        private readonly CustomBlockParser $customBlockParser,
    ) {
    }

    public function transform(TrombinoscopePerson $person)
    {
        $data = $this->partialTransformer->transform($person);
        $data['content'] = $this->customBlockParser->normalizeCustomBlocksIn($person->getContent());

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
