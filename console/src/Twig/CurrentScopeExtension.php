<?php

namespace App\Twig;

use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CurrentScopeExtension extends AbstractExtension
{
    private RequestStack $requestStack;
    private OrganizationRepository $organizationRepository;

    public function __construct(RequestStack $requestStack, OrganizationRepository $organizationRepository)
    {
        $this->requestStack = $requestStack;
        $this->organizationRepository = $organizationRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_current_organization', [$this, 'getCurrentOrganization']),
            new TwigFunction('get_user_organizations_switcher', [$this, 'getUserOrganizationsSwitcher']),
            new TwigFunction('get_current_project', [$this, 'getCurrentProject']),
        ];
    }

    public function getUserOrganizationsSwitcher(User $user): array
    {
        return $this->organizationRepository->findUserSwitcher($user);
    }

    public function getCurrentOrganization(): ?Organization
    {
        return $this->requestStack->getCurrentRequest()->attributes->get('organization');
    }

    public function getCurrentProject(): ?Project
    {
        return $this->requestStack->getCurrentRequest()->attributes->get('project');
    }
}
