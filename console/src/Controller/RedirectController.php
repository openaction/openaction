<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrganizationMemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    #[Route('', name: 'homepage_redirect')]
    #[Route('', name: 'root_url')]
    public function homeRedirect(OrganizationMemberRepository $memberRepository, Request $request)
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('security_login');
        }

        if (!$organizations = $user->getOrganizations()) {
            return $this->render('console/subscription/no_orga.html.twig');
        }

        $organization = $organizations[0];

        if (!$request->query->getBoolean('force_orga')) {
            $projects = $organization->filterAccessibleProjects(
                $organization->getProjects(),
                $memberRepository->findMember($user, $organization)
            );

            if (1 === $projects->count()) {
                return $this->redirectToRoute('console_project_home_start', ['projectUuid' => $projects[0]->getUuid()]);
            }
        }

        return $this->redirectToRoute('console_organization_projects', ['organizationUuid' => $organization->getUuid()]);
    }
}
