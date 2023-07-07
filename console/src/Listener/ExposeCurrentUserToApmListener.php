<?php

namespace App\Listener;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ExposeCurrentUserToApmListener implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $user = $this->security->getUser();

        $type = 'user';
        $uuid = 'anonymous';
        if ($user instanceof User) {
            $uuid = $user->getUuid()->toRfc4122();
        } elseif ($user instanceof Project) {
            $type = 'project';
            $uuid = $user->getUuid()->toRfc4122();
        }

        $event->getResponse()->headers->set('X-Current-User-Type', $type);
        $event->getResponse()->headers->set('X-Current-User-Id', $uuid);
    }
}
