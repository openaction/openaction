<?php

namespace App\Sentry;

use App\Messenger\Stamp\QueueTimeStamp;
use App\Messenger\Stamp\StartTimeStamp;
use App\Messenger\Stamp\UniqueIdStamp;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class MessengerSentryListener implements EventSubscriberInterface
{
    private const MAX_CHILDREN = 50;
    private const MAX_DEPTH = 4;
    private const MAX_SERIALIZED_LENGTH = 8000;
    private const MAX_STRING_LENGTH = 2048;

    /**
     * @var array<string, bool>
     */
    private array $activeScopes = [];

    public function __construct(private HubInterface $hub)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageReceivedEvent::class => ['onMessageReceived', 100],
            WorkerMessageHandledEvent::class => ['onMessageHandled', -100],
            WorkerMessageFailedEvent::class => ['onMessageFailed', -100],
        ];
    }

    public function onMessageReceived(WorkerMessageReceivedEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $scopeKey = $this->getEnvelopeKey($envelope);

        $scope = $this->hub->pushScope();

        $message = $envelope->getMessage();

        $scope->setTag('messenger_message_class', $message::class);

        if ($uniqueId = $envelope->last(UniqueIdStamp::class)) {
            $scope->setTag('messenger_unique_id', $uniqueId->getUniqueId());
        }

        foreach ($this->extractProjectTags($message) as $key => $value) {
            $scope->setTag($key, $value);
        }

        foreach ($this->extractOrganizationTags($message) as $key => $value) {
            $scope->setTag($key, $value);
        }

        $scope->setExtra('messenger_stamps', $this->describeStamps($envelope));

        $payload = $this->serializePayload($message);
        $scope->setExtra('messenger_payload', $payload['body']);

        if ($payload['truncated']) {
            $scope->setExtra('messenger_payload_truncated', true);
        }

        $this->activeScopes[$scopeKey] = true;
    }

    public function onMessageHandled(WorkerMessageHandledEvent $event): void
    {
        $this->popScope($event->getEnvelope());
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        if ($this->isScopeActive($event->getEnvelope())) {
            $this->hub->configureScope(function (Scope $scope) use ($event) {
                $scope->setExtra('messenger_failure_reason', $event->getThrowable()->getMessage());
            });
        }

        $this->popScope($event->getEnvelope());
    }

    private function popScope(Envelope $envelope): void
    {
        $key = $this->getEnvelopeKey($envelope);

        if (!$this->isScopeActive($envelope)) {
            return;
        }

        unset($this->activeScopes[$key]);
        $this->hub->popScope();
    }

    private function isScopeActive(Envelope $envelope): bool
    {
        return isset($this->activeScopes[$this->getEnvelopeKey($envelope)]);
    }

    private function getEnvelopeKey(Envelope $envelope): string
    {
        /** @var UniqueIdStamp|null $uniqueIdStamp */
        $uniqueIdStamp = $envelope->last(UniqueIdStamp::class);

        if ($uniqueIdStamp) {
            return $uniqueIdStamp->getUniqueId();
        }

        return spl_object_hash($envelope->getMessage());
    }

    private function extractProjectTags(object $message): array
    {
        $tags = [];

        if ($id = $this->readStringValue($message, ['projectId', 'projectUuid', 'project_id', 'project_uuid'], ['getProjectId', 'getProjectUuid'])) {
            $tags['project_id'] = $id;
        }

        if ($name = $this->readStringValue($message, ['projectName', 'project_name'], ['getProjectName'])) {
            $tags['project_name'] = $name;
        }

        return $tags;
    }

    private function extractOrganizationTags(object $message): array
    {
        $tags = [];

        if ($id = $this->readStringValue($message, ['organizationId', 'organizationUuid', 'organization_id', 'organization_uuid'], ['getOrganizationId', 'getOrganizationUuid'])) {
            $tags['organization_id'] = $id;
        }

        if ($name = $this->readStringValue($message, ['organizationName', 'organization_name'], ['getOrganizationName'])) {
            $tags['organization_name'] = $name;
        }

        return $tags;
    }

    private function describeStamps(Envelope $envelope): array
    {
        /** @var QueueTimeStamp|null $queueTimeStamp */
        $queueTimeStamp = $envelope->last(QueueTimeStamp::class);

        /** @var StartTimeStamp|null $startTimeStamp */
        $startTimeStamp = $envelope->last(StartTimeStamp::class);

        /** @var ReceivedStamp|null $receivedStamp */
        $receivedStamp = $envelope->last(ReceivedStamp::class);

        /** @var UniqueIdStamp|null $uniqueIdStamp */
        $uniqueIdStamp = $envelope->last(UniqueIdStamp::class);

        return array_filter([
            'unique_id' => $uniqueIdStamp?->getUniqueId(),
            'receiver' => $receivedStamp?->getTransportName(),
            'queue_time_ms' => $queueTimeStamp ? $this->durationSince($queueTimeStamp->getQueueTime()) : null,
            'handling_time_ms' => $startTimeStamp ? $this->durationSince($startTimeStamp->getStartTime()) : null,
        ], static fn ($value) => null !== $value && '' !== $value);
    }

    private function durationSince(float $timestamp): int
    {
        return (int) round(max(0, (microtime(true) - $timestamp) * 1000));
    }

    /**
     * @return array{body: string, truncated: bool}
     */
    private function serializePayload(object $message): array
    {
        $normalized = $this->normalizeValue($message);
        $truncated = false;
        $encoded = null;

        try {
            $encoded = json_encode($normalized, JSON_THROW_ON_ERROR | JSON_PARTIAL_OUTPUT_ON_ERROR);
        } catch (\Throwable) {
            // Fallback to serialized payload below.
        }

        if (null === $encoded) {
            try {
                $encoded = serialize($normalized);
            } catch (\Throwable) {
                $encoded = '[unserializable payload]';
            }
        }

        if (strlen($encoded) > self::MAX_SERIALIZED_LENGTH) {
            $encoded = substr($encoded, 0, self::MAX_SERIALIZED_LENGTH).'...[truncated]';
            $truncated = true;
        }

        return [
            'body' => $encoded,
            'truncated' => $truncated,
        ];
    }

    private function normalizeValue(mixed $value, int $depth = 0): mixed
    {
        if ($depth >= self::MAX_DEPTH) {
            return '...';
        }

        if (is_string($value)) {
            return $this->truncateString($value);
        }

        if (is_scalar($value) || null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if ($value instanceof \Stringable) {
            return $this->truncateString((string) $value);
        }

        if (is_array($value)) {
            return $this->normalizeArray($value, $depth);
        }

        if (is_object($value)) {
            return $this->normalizeObject($value, $depth);
        }

        return gettype($value);
    }

    private function normalizeArray(array $values, int $depth): array
    {
        $normalized = [];
        $count = 0;

        foreach ($values as $key => $value) {
            if ($count >= self::MAX_CHILDREN) {
                $normalized['__truncated'] = true;
                break;
            }

            $normalized[$key] = $this->isSensitiveKey((string) $key)
                ? '[REDACTED]'
                : $this->normalizeValue($value, $depth + 1);

            ++$count;
        }

        return $normalized;
    }

    private function normalizeObject(object $value, int $depth): array|string
    {
        if ($value instanceof \JsonSerializable) {
            return $this->normalizeValue($value->jsonSerialize(), $depth + 1);
        }

        $normalized = [];

        try {
            $reflection = new \ReflectionObject($value);
        } catch (\Throwable) {
            return $value::class;
        }

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if (count($normalized) >= self::MAX_CHILDREN) {
                $normalized['__truncated'] = true;

                break;
            }

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if ($this->isSensitiveKey($propertyName)) {
                $normalized[$propertyName] = '[REDACTED]';

                continue;
            }

            if (method_exists($property, 'isInitialized') && !$property->isInitialized($value)) {
                continue;
            }

            try {
                $propertyValue = $property->getValue($value);
            } catch (\Throwable) {
                $normalized[$propertyName] = '[uninitialized]';

                continue;
            }

            $normalized[$propertyName] = $this->normalizeValue($propertyValue, $depth + 1);
        }

        return $normalized;
    }

    private function truncateString(string $value): string
    {
        if (strlen($value) > self::MAX_STRING_LENGTH) {
            return substr($value, 0, self::MAX_STRING_LENGTH).'...[truncated]';
        }

        return $value;
    }

    private function readStringValue(object $message, array $properties, array $methods): ?string
    {
        $value = $this->readRawValue($message, $properties, $methods);

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

    private function readRawValue(object $message, array $properties, array $methods): mixed
    {
        foreach ($methods as $method) {
            if (method_exists($message, $method)) {
                return $message->{$method}();
            }
        }

        foreach ($properties as $property) {
            if (property_exists($message, $property)) {
                return $message->{$property};
            }
        }

        return null;
    }

    private function isSensitiveKey(string $key): bool
    {
        $key = strtolower($key);

        foreach (['password', 'token', 'secret', 'apikey', 'api_key', 'authorization', 'auth'] as $sensitive) {
            if (str_contains($key, $sensitive)) {
                return true;
            }
        }

        return false;
    }
}
