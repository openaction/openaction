<?php

namespace App\Tests\Kernel;

use App\Messenger\TimeMiddleware;
use App\Messenger\UniqueIdMiddleware;
use Symfony\Bridge\Doctrine\Messenger\DoctrineCloseConnectionMiddleware;
use Symfony\Bridge\Doctrine\Messenger\DoctrinePingConnectionMiddleware;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\TraceableMessageBus;

class MessengerConfigurationTest extends KernelTestCase
{
    public function testDefaultBusKeepsDoctrineConnectionProtection(): void
    {
        self::bootKernel();

        $middlewares = $this->extractMiddlewares(static::getContainer()->get(MessageBusInterface::class));
        $middlewareClasses = array_map(static fn (MiddlewareInterface $middleware) => $middleware::class, $middlewares);

        $pingIndex = array_search(DoctrinePingConnectionMiddleware::class, $middlewareClasses, true);
        $closeIndex = array_search(DoctrineCloseConnectionMiddleware::class, $middlewareClasses, true);
        $uniqueIndex = array_search(UniqueIdMiddleware::class, $middlewareClasses, true);
        $timeIndex = array_search(TimeMiddleware::class, $middlewareClasses, true);

        $this->assertIsInt($pingIndex);
        $this->assertIsInt($closeIndex);
        $this->assertIsInt($uniqueIndex);
        $this->assertIsInt($timeIndex);

        $this->assertLessThan($uniqueIndex, $pingIndex);
        $this->assertLessThan($uniqueIndex, $closeIndex);
        $this->assertLessThan($timeIndex, $closeIndex);
    }

    /**
     * @return list<MiddlewareInterface>
     */
    private function extractMiddlewares(MessageBusInterface $bus): array
    {
        while ($bus instanceof TraceableMessageBus) {
            $decoratedBusProperty = new \ReflectionProperty($bus, 'decoratedBus');
            $decoratedBusProperty->setAccessible(true);
            $bus = $decoratedBusProperty->getValue($bus);
        }

        $this->assertInstanceOf(MessageBus::class, $bus);

        $aggregateProperty = new \ReflectionProperty(MessageBus::class, 'middlewareAggregate');
        $aggregateProperty->setAccessible(true);
        $middlewareAggregate = $aggregateProperty->getValue($bus);
        $iterator = $middlewareAggregate->getIterator();

        while ($iterator instanceof \IteratorAggregate) {
            $iterator = $iterator->getIterator();
        }

        $middlewares = [];
        foreach ($iterator as $middleware) {
            $middlewares[] = $middleware;
        }

        return $middlewares;
    }
}
