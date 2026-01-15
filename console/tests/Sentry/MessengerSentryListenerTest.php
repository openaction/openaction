<?php

namespace App\Tests\Sentry;

use App\Messenger\Stamp\QueueTimeStamp;
use App\Messenger\Stamp\StartTimeStamp;
use App\Messenger\Stamp\UniqueIdStamp;
use App\Sentry\MessengerSentryListener;
use PHPUnit\Framework\TestCase;
use Sentry\ClientBuilder;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\State\Hub;
use Sentry\State\Scope;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class MessengerSentryListenerTest extends TestCase
{
    public function testScopeIsPopulatedAndCleared(): void
    {
        $hub = new Hub(ClientBuilder::create(['dsn' => 'http://public@example.com/1'])->getClient());
        $listener = new MessengerSentryListener($hub);

        $message = new DummyMessage();
        $envelope = (new Envelope($message))
            ->with(new UniqueIdStamp())
            ->with(new QueueTimeStamp())
            ->with(new StartTimeStamp())
            ->with(new ReceivedStamp('async'));

        $listener->onMessageReceived(new WorkerMessageReceivedEvent($envelope, 'async'));

        $event = Event::createEvent();
        $hint = EventHint::fromArray([]);
        $hub->configureScope(function (Scope $scope) use ($event, $hint) {
            $scope->applyToEvent($event, $hint);
        });

        $tags = $event->getTags();
        $extras = $event->getExtra();

        $this->assertSame(DummyMessage::class, $tags['messenger_message_class']);
        $this->assertSame('42', $tags['project_id']);
        $this->assertSame('orga-1', $tags['organization_id']);
        $this->assertArrayHasKey('messenger_unique_id', $tags);

        $this->assertArrayHasKey('messenger_stamps', $extras);
        $this->assertIsArray($extras['messenger_stamps']);
        $this->assertArrayHasKey('queue_time_ms', $extras['messenger_stamps']);
        $this->assertArrayHasKey('handling_time_ms', $extras['messenger_stamps']);

        $this->assertArrayHasKey('messenger_payload', $extras);
        $this->assertStringContainsString('example payload', $extras['messenger_payload']);
        $this->assertStringNotContainsString('top-secret', $extras['messenger_payload']);

        $listener->onMessageHandled(new WorkerMessageHandledEvent($envelope, 'async'));

        $cleanEvent = Event::createEvent();
        $hub->configureScope(function (Scope $scope) use ($cleanEvent, $hint) {
            $scope->applyToEvent($cleanEvent, $hint);
        });

        $this->assertArrayNotHasKey('messenger_message_class', $cleanEvent->getTags());
    }
}

class DummyMessage
{
    public string $projectId = '42';
    public string $organizationUuid = 'orga-1';
    public string $payload = 'example payload';
    public string $token = 'top-secret';

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getOrganizationUuid(): string
    {
        return $this->organizationUuid;
    }
}
