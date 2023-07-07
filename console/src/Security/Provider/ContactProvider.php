<?php

namespace App\Security\Provider;

use App\Entity\Community\Contact;
use App\Repository\Community\ContactRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ContactProvider implements UserProviderInterface
{
    private RequestStack $requestStack;
    private ProjectRepository $projectRepo;
    private ContactRepository $contactRepo;

    public function __construct(RequestStack $requestStack, ProjectRepository $projectRepo, ContactRepository $contactRepo)
    {
        $this->requestStack = $requestStack;
        $this->projectRepo = $projectRepo;
        $this->contactRepo = $contactRepo;
    }

    public function supportsClass(string $class): bool
    {
        return Contact::class === $class;
    }

    public function refreshUser(UserInterface $user): Contact
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function loadUserByUsername(string $username): Contact
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): Contact
    {
        $project = $this->projectRepo->findOneByUuid(
            $this->requestStack->getMainRequest()->attributes->get('projectUuid')
        );

        if (!$project) {
            throw new UserNotFoundException();
        }

        $contact = $this->contactRepo->findOneBy([
            'organization' => $project->getOrganization(),
            'email' => $identifier,
        ]);

        if (!$contact) {
            throw new UserNotFoundException();
        }

        return $contact;
    }
}
