<?php

namespace App\Form\Website\Model;

use App\Entity\Website\Event;
use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class EventData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public ?string $title = null;

    public ?\DateTime $beginAt = null;

    #[Assert\Timezone]
    public ?string $timezone = '';

    public ?string $content = '';

    #[Assert\Length(max: 250)]
    public ?string $url = '';

    #[Assert\Length(max: 35)]
    public ?string $buttonText = '';

    #[Assert\Regex(pattern: '/^-?[0-9]{1,3}\.[0-9]{1,7}$/')]
    public ?string $latitude = null;

    #[Assert\Regex(pattern: '/^-?[0-9]{1,3}\.[0-9]{1,7}$/')]
    public ?string $longitude = null;

    #[Assert\Length(max: 250)]
    public ?string $address = null;

    public bool $hasForm = true;

    public ?string $publishedAt = null;

    #[Assert\Json(groups: ['Metadata'])]
    public ?string $categories = null;

    #[Assert\Json(groups: ['Metadata'])]
    public ?string $participants = null;

    #[Assert\Length(max: 250)]
    #[Assert\Url]
    public ?string $externalUrl = null;

    public ?bool $onlyForMembers = false;

    public function __construct(Event $event)
    {
        $this->title = $event->getTitle();
        $this->beginAt = $event->getBeginAt() ?: new \DateTime('tomorrow 18:00');
        $this->timezone = $event->getTimezone() ?: 'Europe/Paris';
        $this->content = $event->getContent();
        $this->externalUrl = $event->getExternalUrl();
        $this->url = $event->getUrl();
        $this->buttonText = $event->getButtonText();
        $this->address = $event->getAddress();
        $this->latitude = $event->getLatitude();
        $this->longitude = $event->getLongitude();
        $this->onlyForMembers = $event->isOnlyForMembers();
    }

    public function isPublished()
    {
        return $this->publishedAt && new \DateTime($this->publishedAt) < new \DateTime();
    }

    public function getCategoriesArray()
    {
        return Json::decode($this->categories) ?: [];
    }

    public function getParticipantsArray()
    {
        return Json::decode($this->participants) ?: [];
    }
}
