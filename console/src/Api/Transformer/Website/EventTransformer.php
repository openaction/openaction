<?php

namespace App\Api\Transformer\Website;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Website\Event;
use App\Proxy\DomainRouter;
use App\Util\Uid;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class EventTransformer extends AbstractTransformer
{
    private EventCategoryTransformer $categoryTransformer;
    private CdnRouter $cdnRouter;
    private DomainRouter $domainRouter;

    protected array $availableIncludes = ['categories'];
    protected array $defaultIncludes = ['categories'];

    public function __construct(EventCategoryTransformer $categoryTransformer, CdnRouter $cdnRouter, DomainRouter $domainRouter)
    {
        $this->categoryTransformer = $categoryTransformer;
        $this->cdnRouter = $cdnRouter;
        $this->domainRouter = $domainRouter;
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
        ];
    }

    public function includeCategories(Event $event)
    {
        return $this->collection($event->getCategories()->toArray(), $this->categoryTransformer);
    }
}
