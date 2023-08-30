<?php

namespace App\Bridge\Sentry;

use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Security\Core\Security;

class SentryListener implements EventSubscriberInterface
{
    private HubInterface $hub;
    private Security $security;
    private LoggerInterface $logger;
    private string $deploymentId;

    public function __construct(HubInterface $hub, Security $security, LoggerInterface $logger, string $deploymentId)
    {
        $this->hub = $hub;
        $this->security = $security;
        $this->logger = $logger;
        $this->deploymentId = $deploymentId;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1],
            KernelEvents::CONTROLLER => ['onKernelController', 10000],
            KernelEvents::TERMINATE => ['onKernelTerminate', 1],
            ConsoleEvents::COMMAND => ['onConsoleCommand', 1],

            // Listen to Messenger events to reset logger after each message and actually dispatch errors to Sentry
            // It should be called after \Symfony\Component\Messenger\EventListener\SendFailedMessageToFailureTransportListener
            // So that we have as much information as we can
            WorkerMessageFailedEvent::class => ['onMessageFailed', -200],
            WorkerMessageHandledEvent::class => 'onMessageHandled',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $deploymentId = $this->deploymentId;
        $this->hub->configureScope(static function (Scope $scope) use ($deploymentId) {
            $scope->setTag('deployment_id', $deploymentId.':request');
        });

        if (!$event->isMainRequest() || !$user = $this->security->getUser()) {
            return;
        }

        $userData = [];
        $userData['type'] = (new \ReflectionClass($user))->getShortName();
        $userData['user_id'] = $user->getUserIdentifier();
        $userData['roles'] = $user->getRoles();

        $this->hub->configureScope(static function (Scope $scope) use ($userData) {
            $scope->setUser($userData, true);
        });
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
        $deploymentId = $this->deploymentId;
        $this->hub->configureScope(static function (Scope $scope) use ($deploymentId) {
            $scope->setTag('deployment_id', $deploymentId.':command');
        });

        $command = $event->getCommand();
        $command = $command ? $command->getName() : 'N/A';
        $command = $command ?? 'N/A';

        $this->hub->configureScope(static function (Scope $scope) use ($command) {
            $scope->setTag('command', $command);
        });
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event)
    {
        $deploymentId = $this->deploymentId;
        $this->hub->configureScope(static function (Scope $scope) use ($deploymentId) {
            $scope->setTag('deployment_id', $deploymentId.':message');
        });

        $message = $event->getEnvelope()->getMessage();

        $context = [
            'message' => $message,
            'error' => $event->getThrowable()->getMessage(),
            'class' => $message::class,
            'exception' => $event->getThrowable(),
        ];

        $this->logger->error('Error thrown while handling message {class}. Error: "{error}"', $context);

        $this->resetLogger();
    }

    public function onMessageHandled()
    {
        $this->resetLogger();
    }

    public function resetLogger()
    {
        $this->logger->reset();
    }
}
