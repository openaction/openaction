<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\LocalizedPetition;
use App\Entity\Website\Petition;
use App\Util\ReadTime;
use App\Util\Uid;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PetitionPartialTransformer extends AbstractTransformer
{
    public function __construct(
        private readonly PetitionCategoryTransformer $categoryTransformer,
        private readonly CdnRouter $cdnRouter,
    ) {
    }

    public function transform(Petition $petition)
    {
        $item = [
            '_resource' => 'Petition',
            '_links' => [
                'self' => $this->createLink('api_website_petitions_view', ['id' => $petition->getSlug()]),
            ],
            // Petitions identifier is the slug
            'id' => Uid::toBase62($petition->getUuid()),
            'slug' => $petition->getSlug(),
            'published_at' => $petition->getPublishedAt() ? $petition->getPublishedAt()->format(DATE_ATOM) : null,
            'start_at' => $petition->getStartAt() ? $petition->getStartAt()->format(DATE_ATOM) : null,
            'end_at' => $petition->getEndAt() ? $petition->getEndAt()->format(DATE_ATOM) : null,
            'signatures_goal' => $petition->getSignaturesGoal(),
            'signatures_count' => $petition->getSignaturesCount() ?: 0,
            'external_url' => $petition->getExternalUrl(),
        ];

        $item['localizations']['data'] = [];
        foreach ($petition->getLocalizations() as $localized) {
            $item['localizations']['data'][] = $this->transformLocalized($localized, includeContentAndForm: false);
        }

        return $item;
    }

    private function transformLocalized(LocalizedPetition $localized, bool $includeContentAndForm): array
    {
        $data = [
            'id' => Uid::toBase62($localized->getUuid()),
            'locale' => $localized->getLocale(),
            'title' => $localized->getTitle(),
            'description' => $localized->getDescription() ?: null,
            'submit_button_label' => $localized->getSubmitButtonLabel(),
            'optin_label' => $localized->getOptinLabel(),
            'legalities' => $localized->getLegalities(),
            'addressed_to' => $localized->getAddressedTo(),
            'read_time' => ReadTime::inMinutes($localized->getContent()),
            'image' => $localized->getImage() ? $this->cdnRouter->generateUrl($localized->getImage()) : null,
            'sharer' => $localized->getImage() ? $this->cdnRouter->generateUrl($localized->getImage(), 'sharer') : null,
        ];

        // Categories of the localized petition
        $data['categories']['data'] = [];
        foreach ($localized->getCategories() as $category) {
            $data['categories']['data'][] = $this->categoryTransformer->transform($category);
        }

        if ($includeContentAndForm) {
            // Filled in PetitionFullTransformer; here we keep it out for list
            $data['content'] = $localized->getContent() ?? '';
            $data['form'] = null;
        }

        return $data;
    }

    public static function describeResourceName(): string
    {
        return 'PetitionPartial';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            '_links' => [
                'self' => 'string',
            ],
            'id' => 'string',
            'slug' => 'string',
            'published_at' => '?string',
            'start_at' => '?string',
            'end_at' => '?string',
            'signatures_goal' => '?integer',
            'signatures_count' => '?integer',
            'external_url' => '?string',
            'localizations' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['type' => 'object']),
                ]),
            ],
        ];
    }
}
