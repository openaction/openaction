<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\TrombinoscopePerson;
use App\Util\Uid;
use App\Website\CustomBlockParser;

class TrombinoscopePersonPartialTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['categories'];
    protected array $defaultIncludes = ['categories'];

    public function __construct(
        private readonly TrombinoscopeCategoryTransformer $categoryTransformer,
        private readonly CdnRouter $cdnRouter,
        private readonly CustomBlockParser $customBlockParser,
    ) {
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
            'description' => $person->getDescription() ?: null,
            'content' => $this->customBlockParser->normalizeCustomBlocksIn($person->getContent()),
            'position' => $person->getWeight(),
            'socialWebsite' => $person->getSocialWebsite() ?: null,
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
        ];
    }

    public function includeCategories(TrombinoscopePerson $person)
    {
        return $this->collection($person->getCategories()->toArray(), $this->categoryTransformer);
    }
}
