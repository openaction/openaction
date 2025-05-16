<?php

namespace App\Maintenance;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;

class ActiveMaintenanceListener implements EventSubscriberInterface, ServiceSubscriberInterface
{
    private ContainerInterface $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    public static function getSubscribedServices(): array
    {
        return [
            Environment::class,
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof ActiveMaintenanceException) {
            return;
        }

        $event->setResponse(new Response($this->locator->get(Environment::class)->render('console/maintenance.html.twig')));
    }
}
