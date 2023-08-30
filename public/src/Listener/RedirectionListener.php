<?php

namespace App\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1023],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$project = $event->getRequest()->attributes->get('project')) {
            return;
        }

        $path = $event->getRequest()->getPathInfo();

        foreach ($project->redirections as $redirection) {
            if ($target = $this->shouldRedirect($redirection['source'], $path, $redirection['target'])) {
                $event->setResponse(new RedirectResponse($target, $redirection['code']));

                return;
            }
        }
    }

    private function shouldRedirect(string $source, string $path, string $target): ?string
    {
        $regex = '~^'.str_replace('\\*', '([^/]+)', preg_quote($source, '~')).'$~i';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        // The regex matches: allow to use $1, $2, ... variables in the target
        unset($matches[0]);

        $replacements = [];
        foreach ($matches as $key => $value) {
            $replacements['$'.$key] = $value;
        }

        return str_replace(array_keys($replacements), array_values($replacements), $target);
    }
}
