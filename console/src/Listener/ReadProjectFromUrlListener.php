<?php

namespace App\Listener;

use App\Repository\OrganizationMemberRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ReadProjectFromUrlListener implements EventSubscriberInterface
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
        private OrganizationMemberRepository $organizationMemberRepository,
        private ProjectRepository $projectRepository,
        private Security $security,
    ) {
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

        if ($projectUuid = $request->attributes->get('projectUuid')) {
            if (!$project = $this->projectRepository->findOneByUuid($projectUuid)) {
                throw new NotFoundHttpException();
            }

            // If there is a project UUID, authentication must be required and the user must be in the orga
            if (!$user = $this->security->getUser()) {
                throw new NotFoundHttpException();
            }

            if (!$this->organizationMemberRepository->findMember($user, $project->getOrganization())) {
                throw new NotFoundHttpException();
            }

            $request->attributes->set('project', $project);
            $request->attributes->set('organization', $project->getOrganization());

            return;
        }

        if ($organizationUuid = $request->attributes->get('organizationUuid')) {
            if (!$organization = $this->organizationRepository->findOneByUuid($organizationUuid)) {
                throw new NotFoundHttpException();
            }

            // If there is an organization UUID, authentication must be required and the user must be in the orga
            if (!$user = $this->security->getUser()) {
                throw new NotFoundHttpException();
            }

            if (!$this->organizationMemberRepository->findMember($user, $organization)) {
                throw new NotFoundHttpException();
            }

            $request->attributes->set('organization', $organization);
        }
    }
}
