<?php

namespace App\Security\Registration;

use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Entity\Registration;
use App\Entity\User;
use App\Mailer\PlatformMailer;
use App\Repository\OrganizationMemberRepository;
use App\Repository\UserRepository;
use App\Search\TenantTokenManager;
use Doctrine\ORM\EntityManagerInterface;

class InviteManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private OrganizationMemberRepository $memberRepository,
        private PlatformMailer $mailer,
        private TenantTokenManager $tenantTokenManager,
    ) {
    }

    public function invite(Organization $orga, User $owner, string $email, bool $isAdmin = true, array $permissions = [], array $permissionsCategories = [], string $locale = 'en')
    {
        $invited = $this->userRepository->findOneByEmail($email);

        // If the invited already has an account, use it
        if ($invited instanceof User) {
            // If the invited is already a member, update the permissions
            if ($member = $this->memberRepository->findMember($invited, $orga)) {
                $member->setPermissions($isAdmin, array_merge($member->getRawProjectsPermissions(), $permissions));
                $member->setProjectsPermissionsCategories($permissionsCategories);
                $this->tenantTokenManager->refreshMemberCrmTenantToken($member, persist: true);

                return $member;
            }

            $member = new OrganizationMember($orga, $invited, $isAdmin, $permissions);
            $member->setProjectsPermissionsCategories($permissionsCategories);
            $this->tenantTokenManager->refreshMemberCrmTenantToken($member, persist: true);

            $this->mailer->sendOrganizationInviteToRegisteredUser($orga, $invited, $owner);

            return $member;
        }

        // Otherwise, create a registration token
        $registration = new Registration($email, $orga, $isAdmin, $permissions, $locale);
        $registration->setProjectsPermissionsCategories($permissionsCategories);
        $this->em->persist($registration);
        $this->em->flush();

        $this->mailer->sendOrganizationInvite($orga, $registration, $owner);

        return $registration;
    }
}
