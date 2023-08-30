<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\TrombinoscopePerson;
use App\Util\Uid;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class TrombinoscopePersonPartialTransformer extends AbstractTransformer
{
    private TrombinoscopeCategoryTransformer $categoryTransformer;
    private CdnRouter $cdnRouter;

    protected array $availableIncludes = ['categories'];
    protected array $defaultIncludes = ['categories'];

    public function __construct(TrombinoscopeCategoryTransformer $categoryTransformer, CdnRouter $cdnRouter)
    {
        $this->categoryTransformer = $categoryTransformer;
        $this->cdnRouter = $cdnRouter;
    }

    public function transform(TrombinoscopePerson $person)
    {
        return [
            '_resource' => 'TrombinoscopePerson',
            '_links' => [
                'self' => $this->createLink('api_website_trombinoscope_view', ['id' => Uid::toBase62($person->getUuid())]),
            ],
            'id' => Uid::toBase62($person->getUuid()),
            'slug' => $person->getSlug(),
            'fullName' => $person->getFullName(),
            'role' => $person->getRole() ?: null,
            'position' => $person->getWeight(),
            'socialEmail' => $person->getSocialEmail() ?: null,
            'socialFacebook' => $person->getSocialFacebook() ?: null,
            'socialTwitter' => $person->getSocialTwitter() ?: null,
            'socialInstagram' => $person->getSocialInstagram() ?: null,
            'socialLinkedIn' => $person->getSocialLinkedIn() ?: null,
            'socialYoutube' => $person->getSocialYoutube() ?: null,
            'socialMedium' => $person->getSocialMedium() ?: null,
            'socialTelegram' => $person->getSocialTelegram() ?: null,
            'image' => $person->getImage() ? $this->cdnRouter->generateUrl($person->getImage()) : null,
            'published_at' => $person->getPublishedAt()?->format(\DateTime::ATOM),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'TrombinoscopePersonPartial';
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
            'categories' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/TrombinoscopeCategory']),
                ]),
            ],
        ];
    }

    public function includeCategories(TrombinoscopePerson $person)
    {
        return $this->collection($person->getCategories()->toArray(), $this->categoryTransformer);
    }
}
