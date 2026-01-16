<?php

namespace App\Sentry;

use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class HttpRequestScopeSubscriber implements EventSubscriberInterface
{
    public function __construct(private HubInterface $hub)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -128],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $project = $request->attributes->get('project');
        $organization = $request->attributes->get('organization') ?? $this->readRawValue(
            $request->attributes->get('project'),
            ['organization'],
            ['getOrganization']
        );

        $this->hub->configureScope(function (Scope $scope) use ($request, $project, $organization) {
            $scope->setTags(array_filter([
                'route' => (string) $request->attributes->get('_route', 'n/a'),
                'host' => $request->getHost(),
            ]));

            foreach ($this->extractProjectTags($project) as $key => $value) {
                $scope->setTag($key, $value);
            }

            foreach ($this->extractOrganizationTags($organization) as $key => $value) {
                $scope->setTag($key, $value);
            }
        });
    }

    private function extractProjectTags(mixed $project): array
    {
        if (null === $project) {
            return [];
        }

        $tags = [];

        if ($id = $this->readStringValue($project, ['id', 'uuid'], ['getId', 'getUuid'])) {
            $tags['project_id'] = $id;
        }

        if ($name = $this->readStringValue($project, ['name'], ['getName'])) {
            $tags['project_name'] = $name;
        }

        return $tags;
    }

    private function extractOrganizationTags(mixed $organization): array
    {
        if (null === $organization) {
            return [];
        }

        $tags = [];

        if ($id = $this->readStringValue($organization, ['id', 'uuid'], ['getId', 'getUuid'])) {
            $tags['organization_id'] = $id;
        }

        if ($name = $this->readStringValue($organization, ['name'], ['getName'])) {
            $tags['organization_name'] = $name;
        }

        return $tags;
    }

    private function readStringValue(mixed $subject, array $properties, array $methods): ?string
    {
        $value = $this->readRawValue($subject, $properties, $methods);

        return $this->stringify($value);
    }

    private function readRawValue(mixed $subject, array $properties, array $methods): mixed
    {
        foreach ($methods as $method) {
            if (is_object($subject) && method_exists($subject, $method)) {
                return $subject->{$method}();
            }
        }

        foreach ($properties as $property) {
            if (is_array($subject) && array_key_exists($property, $subject)) {
                return $subject[$property];
            }

            if (is_object($subject) && property_exists($subject, $property)) {
                return $subject->{$property};
            }
        }

        return null;
    }

    private function stringify(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        return null;
    }
}
