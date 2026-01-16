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
    public function testTagsAreAddedFromRequestContext(): void
    {
        $hub = new Hub(ClientBuilder::create(['dsn' => 'http://public@example.com/1'])->getClient());
        $subscriber = new HttpRequestScopeSubscriber($hub);

        $project = new class {
            public function getId(): int
            {
                return 42;
            }

            public function getName(): string
            {
                return 'Demo project';
            }

            public function getOrganization(): object
            {
                return new class {
                    public function getId(): int
                    {
                        return 9;
                    }

                    public function getName(): string
                    {
                        return 'Demo org';
                    }
                };
            }
        };

        $request = Request::create('https://example.test/demo');
        $request->attributes->add(['_route' => 'demo_route', 'project' => $project]);

        $event = new RequestEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
        $subscriber->onKernelRequest($event);

        $eventPayload = Event::createEvent();
        $hint = EventHint::fromArray([]);
        $hub->configureScope(function (Scope $scope) use ($eventPayload, $hint) {
            $scope->applyToEvent($eventPayload, $hint);
        });

        $tags = $eventPayload->getTags();

        $this->assertSame('demo_route', $tags['route']);
        $this->assertSame('example.test', $tags['host']);
        $this->assertSame('42', $tags['project_id']);
        $this->assertSame('Demo project', $tags['project_name']);
        $this->assertSame('9', $tags['organization_id']);
        $this->assertSame('Demo org', $tags['organization_name']);
    }
}
