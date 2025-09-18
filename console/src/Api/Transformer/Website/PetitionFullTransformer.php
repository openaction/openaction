<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Website\LocalizedPetition;
use App\Entity\Website\Petition;
use App\Util\ReadTime;
use App\Util\Uid;
use App\Website\CustomBlockParser;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class PetitionFullTransformer extends AbstractTransformer
{
    public function __construct(
        private readonly PetitionPartialTransformer $partialTransformer,
        private readonly FormFullTransformer $formFullTransformer,
        private readonly CustomBlockParser $customBlockParser,
    ) {
    }

    public function transform(Petition $petition)
    {
        $data = $this->partialTransformer->transform($petition);

        // Add content and form to localizations inline
        $data['localizations']['data'] = [];
        foreach ($petition->getLocalizations() as $localized) {
            $data['localizations']['data'][] = $this->transformLocalized($localized);
        }

        return $data;
    }

    private function transformLocalized(LocalizedPetition $localized): array
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
            'content' => $this->customBlockParser->normalizeCustomBlocksIn($localized->getContent() ?? ''),
        ];

        // Inline form
        $data['form'] = $localized->getForm() ? $this->formFullTransformer->transform($localized->getForm()) : null;

        // Keep same mapping for categories/images as partial
        $partial = $this->partialTransformer->transform($localized->getPetition());
        foreach ($localized->getPetition()->getLocalizations() as $lp) {
            if ($lp === $localized) {
                $partialLocalized = $this->extractMatchingLocalization($partial, $lp->getLocale());
                $data['image'] = $partialLocalized['image'] ?? null;
                $data['sharer'] = $partialLocalized['sharer'] ?? null;
                $data['categories'] = $partialLocalized['categories'] ?? ['data' => []];
                break;
            }
        }

        return $data;
    }

    private function extractMatchingLocalization(array $partial, string $locale): array
    {
        foreach ($partial['localizations']['data'] ?? [] as $loc) {
            if (($loc['locale'] ?? null) === $locale) {
                return $loc;
            }
        }

        return [];
    }

    public static function describeResourceName(): string
    {
        return 'PetitionFull';
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
