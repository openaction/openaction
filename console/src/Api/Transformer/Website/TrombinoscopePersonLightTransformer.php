<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\TrombinoscopePerson;
use App\Util\Uid;

class TrombinoscopePersonLightTransformer extends AbstractTransformer
{
    public function __construct(private readonly CdnRouter $cdnRouter)
    {
    }

    public function transform(TrombinoscopePerson $person)
    {
        return [
            '_resource' => 'TrombinoscopePersonLight',
            '_links' => [
                'self' => $this->createLink('api_website_trombinoscope_view', ['id' => Uid::toBase62($person->getUuid())]),
            ],
            'id' => Uid::toBase62($person->getUuid()),
            'slug' => $person->getSlug(),
            'fullName' => $person->getFullName(),
            'position' => $person->getWeight(),
            'image' => $person->getImage() ? $this->cdnRouter->generateUrl($person->getImage()) : null,
        ];
    }

    public static function describeResourceName(): string
    {
        return 'TrombinoscopePersonLight';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'self' => 'string',
            ],
            'id' => 'string',
        ];
    }
}
