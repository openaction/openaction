<?php

namespace App\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ReadLocaleFromSessionListener implements EventSubscriberInterface
{
    private string $defaultLocale;

    public function __construct(string $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 21], // To execute before the default LocaleListener
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Ignore CDN, API and webhooks
        if (str_starts_with($request->getRequestUri(), '/serve')
            || str_starts_with($request->getRequestUri(), '/theme')
            || str_starts_with($request->getRequestUri(), '/api')
            || str_starts_with($request->getRequestUri(), '/webhook')) {
            return;
        }

        // If the locale is forced using a query parameter, use it
        if ($locale = $request->query->get('_locale')) {
            $request->setLocale($locale);
        }

        if (!$request->hasPreviousSession()) {
            return;
        }

        // Then if the locale was forced and the session is available, also store it in session
        if ($locale = $request->query->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        }

        $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
    }
}
