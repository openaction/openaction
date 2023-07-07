<?php

namespace App\Form\Community\Model;

use App\Entity\Community\TextingCampaign;
use App\Util\Json;
use App\Util\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

class TextingCampaignMetaData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 140)]
    public ?string $content = null;

    public ?string $onlyForMembers = null;

    public ?string $areasFilterIds = null;

    public ?string $tagsFilter = null;

    public ?string $tagsFilterType = null;

    public ?string $contactsFilter = null;

    public static function createFromCampaign(TextingCampaign $campaign): self
    {
        $self = new self();
        $self->content = $campaign->getContent();
        $self->onlyForMembers = $campaign->isOnlyForMembers() ? '1' : '0';

        $tags = [];
        foreach ($campaign->getTagsFilter() as $tag) {
            $id = $tag->getId();
            $tags[] = ['id' => (string) $id, 'name' => $tag->getName(), 'slug' => $tag->getSlug()];
        }

        $self->tagsFilter = $tags ? Json::encode($tags) : '';
        $self->tagsFilterType = $campaign->getTagsFilterType();

        $areas = [];
        foreach ($campaign->getAreasFilter() as $area) {
            $id = $area->getId();
            $areas[$id] = ['id' => (string) $id, 'name' => $area->getName(), 'desc' => $area->getDescription()];
        }

        $self->areasFilterIds = $areas ? Json::encode($areas) : '';
        $self->contactsFilter = $campaign->getContactsFilter() ? Json::encode($campaign->getContactsFilter()) : '';

        return $self;
    }

    public function isOnlyForMembers(): bool
    {
        return filter_var($this->onlyForMembers, FILTER_VALIDATE_BOOLEAN);
    }

    public function parseTagsFilter(): array
    {
        if (!$this->tagsFilter) {
            return [];
        }

        try {
            return Json::decode($this->tagsFilter);
        } catch (\Throwable) {
            return [];
        }
    }

    public function parseAreasFilterIds(): array
    {
        if (!$this->areasFilterIds) {
            return [];
        }

        try {
            return array_keys(Json::decode($this->areasFilterIds));
        } catch (\Throwable) {
            return [];
        }
    }

    public function parseContactsFilter(): array
    {
        if (!$this->contactsFilter) {
            return [];
        }

        try {
            $numbers = Json::decode($this->contactsFilter);
        } catch (\Throwable) {
            return [];
        }

        $phones = [];
        foreach (array_slice($numbers, 0, 100) as $number) {
            if ($parsed = PhoneNumber::parse($number, 'FR')) {
                $phones[] = PhoneNumber::formatDatabase($parsed);
            }
        }

        return $phones;
    }
}
