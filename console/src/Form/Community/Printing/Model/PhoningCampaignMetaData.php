<?php

namespace App\Form\Community\Printing\Model;

use App\Entity\Community\Contact;
use App\Entity\Community\PhoningCampaign;
use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class PhoningCampaignMetaData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $endAfter = null;

    public ?string $onlyForMembers = null;

    public ?string $areasFilterIds = null;

    public ?string $tagsFilter = null;

    public ?string $tagsFilterType = null;

    public ?string $contactsFilter = null;

    public static function createFromCampaign(PhoningCampaign $campaign): self
    {
        $self = new self();
        $self->name = $campaign->getName();
        $self->endAfter = $campaign->getEndAfter();
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
            $emails = Json::decode($this->contactsFilter);
        } catch (\Throwable) {
            return [];
        }

        $emails = array_slice($emails, 0, 100);

        array_walk($emails, static function (&$email) {
            $email = Contact::normalizeEmail($email);
        });

        return $emails;
    }
}
