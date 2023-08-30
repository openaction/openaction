<?php

namespace App\Listener;

use App\Entity\User;
use App\Repository\UserVisitRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TrackConsoleVisitListener implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;
    private UserVisitRepository $repository;

    public function __construct(TokenStorageInterface $tokenStorage, UserVisitRepository $repository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Ignore admin, dev tools and CDN
        if (str_starts_with($request->getRequestUri(), '/admin')
            || str_starts_with($request->getRequestUri(), '/_wdt')
            || str_starts_with($request->getRequestUri(), '/_redirect')
            || str_starts_with($request->getRequestUri(), '/_profiler')
            || str_starts_with($request->getRequestUri(), '/serve')) {
            return;
        }

        if (!($token = $this->tokenStorage->getToken()) || !($user = $token->getUser())) {
            return;
        }

        if (!$user instanceof User || in_array('ROLE_PREVIOUS_ADMIN', $token->getRoleNames(), true)) {
            return;
        }

        $this->repository->trackPageView($user);
    }
}
