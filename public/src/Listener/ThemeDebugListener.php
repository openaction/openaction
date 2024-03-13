<?php

namespace App\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Error\Error;

class ThemeDebugListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $e = $event->getThrowable();

        if ($e instanceof Error) {
            $event->setResponse(new Response(
                content: 'Template error in '.$e->getSourceContext()?->getPath().' on line '.$e->getTemplateLine().': '.$e->getMessage(),
                headers: ['Content-Type' => 'text/plain'],
            ));
        }
    }
}
