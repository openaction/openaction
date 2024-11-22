<?php

namespace App\Security\TwoFactor;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class TwoFactorAuthRequiredListener implements EventSubscriberInterface, ServiceSubscriberInterface
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
            UrlGeneratorInterface::class,
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof TwoFactorAuthRequiredException) {
            return;
        }

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->locator->get(UrlGeneratorInterface::class);

        $event->setResponse(new RedirectResponse($urlGenerator->generate('console_user_2fa', ['forced' => '1'])));
    }
}
