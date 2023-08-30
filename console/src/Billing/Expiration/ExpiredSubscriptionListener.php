<?php

namespace App\Billing\Expiration;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;

class ExpiredSubscriptionListener implements EventSubscriberInterface, ServiceSubscriberInterface
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
        if (!$exception instanceof ExpiredSubscriptionException) {
            return;
        }

        $response = new Response('', Response::HTTP_PAYMENT_REQUIRED);
        $response->setContent($this->locator->get(Environment::class)->render('console/subscription/expired.html.twig'));

        $event->setResponse($response);
    }
}
