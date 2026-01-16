<?php

namespace App\Tests\Sentry;

use App\Sentry\HttpRequestScopeSubscriber;
use PHPUnit\Framework\TestCase;
use Sentry\ClientBuilder;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\State\Hub;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HttpRequestScopeSubscriberTest extends TestCase
{
    public function testProjectAndOrganizationContextAreAdded(): void
    {
        $hub = new Hub(ClientBuilder::create(['dsn' => 'http://public@example.com/1'])->getClient());
        $subscriber = new HttpRequestScopeSubscriber($hub);

        $project = new \stdClass();
        $project->id = '123';
        $project->name = 'Public project';
        $project->organization = [
            'id' => '789',
            'name' => 'Public org',
        ];

        $request = Request::create('https://citipo.example/demo');
        $request->attributes->add(['_route' => 'public_route', 'project' => $project]);

        $event = new RequestEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
        $subscriber->onKernelRequest($event);

        $eventPayload = Event::createEvent();
        $hint = EventHint::fromArray([]);
        $hub->configureScope(function (Scope $scope) use ($eventPayload, $hint) {
            $scope->applyToEvent($eventPayload, $hint);
        });

        $tags = $eventPayload->getTags();

        $this->assertSame('public_route', $tags['route']);
        $this->assertSame('citipo.example', $tags['host']);
        $this->assertSame('123', $tags['project_id']);
        $this->assertSame('Public project', $tags['project_name']);
        $this->assertSame('789', $tags['organization_id']);
        $this->assertSame('Public org', $tags['organization_name']);
    }
}
