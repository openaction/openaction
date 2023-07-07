<?php

namespace App\Bridge\Sentry;

use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SentryListener implements EventSubscriberInterface
{
    private HubInterface $hub;
    private LoggerInterface $logger;

    public function __construct(HubInterface $hub, LoggerInterface $logger)
    {
        $this->hub = $hub;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 10000],
            KernelEvents::TERMINATE => ['onKernelTerminate', 1],
            ConsoleEvents::COMMAND => ['onConsoleCommand', 1],
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {
        if (!$event->isMainRequest() || !$event->getRequest()->attributes->has('_route')) {
            return;
        }

        $matchedRoute = (string) $event->getRequest()->attributes->get('_route');

        $this->hub->configureScope(static function (Scope $scope) use ($matchedRoute) {
            $scope->setTag('route', $matchedRoute);
        });
    }

    public function onKernelTerminate(TerminateEvent $event)
    {
        $statusCode = $event->getResponse()->getStatusCode();

        $this->hub->configureScope(static function (Scope $scope) use ($statusCode) {
            $scope->setTag('status_code', (string) $statusCode);
        });

        if ($statusCode >= 500) {
            // 5XX response are private/security data safe so let's log them for debugging purpose
            $this->logger->error('500 returned', ['response' => $event->getResponse()]);
        }
    }

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();
        $command = $command ? $command->getName() : 'N/A';
        $command = $command ?? 'N/A';

        $this->hub->configureScope(static function (Scope $scope) use ($command) {
            $scope->setTag('command', $command);
        });
    }

    public function resetLogger()
    {
        $this->logger->reset();
    }
}
