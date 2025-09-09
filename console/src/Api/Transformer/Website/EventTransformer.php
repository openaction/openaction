<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\Event;
use App\Entity\Website\TrombinoscopePerson;
use App\Proxy\DomainRouter;
use App\Util\Uid;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class EventTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['categories', 'participants'];
    protected array $defaultIncludes = ['categories', 'participants'];

    public function __construct(
        private readonly EventCategoryTransformer $categoryTransformer,
        private readonly TrombinoscopePersonLightTransformer $participantTransformer,
        private readonly CdnRouter $cdnRouter,
        private readonly DomainRouter $domainRouter,
    ) {
    }

    public function transform(Event $event)
    {
        return [
            '_resource' => 'Event',
            '_links' => [
                'self' => (
                    $event->isOnlyForMembers()
                        ? $this->createLink('api_area_events_view', ['id' => Uid::toBase62($event->getUuid())])
                        : $this->createLink('api_website_events_view', ['id' => Uid::toBase62($event->getUuid())])
                ),
            ],
            'id' => Uid::toBase62($event->getUuid()),
            'title' => $event->getTitle(),
            'slug' => $event->getSlug(),
            'content' => $event->getContent(),
            'externalUrl' => $event->getExternalUrl() ?: null,
            'published_at' => $event->getPublishedAt()?->format(\DateTime::ATOM),
            'begin_at' => $event->getBeginAt()?->format(\DateTime::ATOM),
            'timezone' => $event->getTimezone(),
            'url' => $event->getUrl(),
            'buttonText' => $event->getButtonText(),
            'latitude' => $event->getLatitude(),
            'longitude' => $event->getLongitude(),
            'address' => $event->getAddress(),
            'image' => $event->getImage() ? $this->cdnRouter->generateUrl($event->getImage()) : null,
            'sharer' => $event->getImage() ? $this->cdnRouter->generateUrl($event->getImage(), 'sharer') : null,
            'form' => $event->getForm() ? $this->domainRouter->generateRedirectUrl($event->getProject(), 'form', Uid::toBase62($event->getForm()->getUuid())) : null,
        ];
    }

    public static function describeResourceName(): string
    {
        return 'Event';
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
            'slug' => 'string',
            'content' => 'string',
            'externalUrl' => '?string',
            'published_at' => '?string',
            'begin_at' => '?string',
            'url' => '?string',
            'buttonText' => '?string',
            'latitude' => '?string',
            'longitude' => '?string',
            'address' => '?string',
            'image' => '?string',
            'sharer' => '?string',
            'form' => '?string',
            'categories' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/EventCategory']),
                ]),
            ],
            'participants' => [
                'data' => new Property([
                    'type' => 'array',
                    'items' => new Items(['ref' => '#/components/schemas/TrombinoscopePersonLight']),
                ]),
            ],
        ];
    }

    public function includeCategories(Event $event)
    {
        return $this->collection($event->getCategories()->toArray(), $this->categoryTransformer);
    }

    public function includeParticipants(Event $event)
    {
        return $this->collection(
            $event->getParticipants()->filter(static fn (TrombinoscopePerson $p) => $p->isPublished())->toArray(),
            $this->participantTransformer,
        );
    }
}
