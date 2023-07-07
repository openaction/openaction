<?php

namespace App\Controller\Console\Organization;

use App\Controller\AbstractController;
use App\Entity\OrganizationMember;
use App\Entity\Registration;
use App\Form\Organization\MemberInviteType;
use App\Form\Organization\MemberPermissionType;
use App\Form\Organization\Model\MemberInviteData;
use App\Form\Organization\Model\MemberPermissionData;
use App\Platform\Permissions;
use App\Repository\OrganizationMemberRepository;
use App\Repository\RegistrationRepository;
use App\Search\TenantTokenManager;
use App\Security\Registration\InviteManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/organization/{organizationUuid}/team')]
class TeamController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('', name: 'console_organization_team')]
    public function team(OrganizationMemberRepository $repo)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_TEAM_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/organization/team/list.html.twig', [
            'organization' => $this->getOrganization(),
            'team' => $repo->findByOrganizationGrouped($this->getOrganization()),
            'pendingInvites' => count($this->getOrganization()->getInvites()),
        ]);
    }

    #[Route('/invite/member', name: 'console_organization_team_invite_member')]
    public function inviteMember(InviteManager $inviteManager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_TEAM_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $data = new MemberInviteData();
        $data->locale = $this->getUser()->getLocale();

        $form = $this->createForm(MemberInviteType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inviteManager->invite(
                $this->getOrganization(),
                $this->getUser(),
                $data->email,
                $data->isAdmin,
                $data->parseProjectPermissions(),
                $data->locale
            );

            $this->addFlash('success', 'team.invite_success');

            return $this->redirectToRoute('console_organization_team', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
            ]);
        }

        return $this->render('console/organization/team/invite_member.html.twig', [
            'form' => $form->createView(),
            'organization' => $this->getOrganization(),
            'projects' => $this->getOrganization()->getProjects(),
        ]);
    }

    #[Route('/{uuid}/permissions', name: 'console_organization_team_permissions')]
    public function permissions(TenantTokenManager $tenantTokenManager, OrganizationMember $member, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_TEAM_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $data = MemberPermissionData::createFromMember($member);

        $form = $this->createForm(MemberPermissionType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member->applyPermissionsUpdate($data);
            $tenantTokenManager->refreshMemberCrmTenantToken($member, persist: true);

            $this->addFlash('success', 'team.update_success');

            return $this->redirectToRoute('console_organization_team_permissions', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
                'uuid' => $member->getUuid(),
            ]);
        }

        return $this->render('console/organization/team/edit_member.html.twig', [
            'form' => $form->createView(),
            'projects' => $this->getOrganization()->getProjects(),
            'organization' => $this->getOrganization(),
            'member' => $member,
        ]);
    }

    #[Route('/{uuid}/remove', name: 'console_organization_team_remove')]
    public function delete(OrganizationMember $organizationMember, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_TEAM_MANAGE, $this->getOrganization());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $this->em->remove($organizationMember);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_organization_team', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }

    #[Route('/pending', name: 'console_organization_team_pending_invites')]
    public function pending(RegistrationRepository $repository)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_TEAM_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();

        $pendingInvites = $repository->findBy(['organization' => $this->getOrganization()], ['createdAt' => 'DESC']);

        if (0 === count($pendingInvites)) {
            return $this->redirectToRoute('console_organization_team', [
                'organizationUuid' => $this->getOrganization()->getUuid(),
            ]);
        }

        return $this->render('console/organization/team/pending.html.twig', [
            'pendingInvites' => $pendingInvites,
        ]);
    }

    #[Route('/pending/{uuid}/remove', name: 'console_organization_team_pending_remove')]
    public function removePending(Registration $registration, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::ORGANIZATION_TEAM_MANAGE, $this->getOrganization());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        $this->em->remove($registration);
        $this->em->flush();

        $pendingInvites = count($this->getOrganization()->getInvites());

        $redirect = (bool) $pendingInvites ? false : $this->generateUrl('console_organization_team', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true, 'redirect' => $redirect]);
        }

        return $this->redirectToRoute('console_organization_team_pending_invites', [
            'organizationUuid' => $this->getOrganization()->getUuid(),
        ]);
    }
}
