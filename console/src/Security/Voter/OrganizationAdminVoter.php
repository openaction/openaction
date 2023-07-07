<?php

namespace App\Security\Voter;

use App\Entity\Organization;
use App\Entity\User;
use App\Platform\Permissions;
use App\Repository\OrganizationMemberRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrganizationAdminVoter extends Voter
{
    public function __construct(private OrganizationMemberRepository $memberRepository)
    {
    }

    public const SUPPORTED_ATTRIBUTES = [
        Permissions::ORGANIZATION_PROJECT_MANAGE,
        Permissions::ORGANIZATION_SEE_CREDITS,
        Permissions::ORGANIZATION_TEAM_MANAGE,
        Permissions::ORGANIZATION_COMMUNITY_MANAGE,
        Permissions::ORGANIZATION_BILLING_MANAGE,
    ];

    protected function supports($action, $entity): bool
    {
        return $entity instanceof Organization && in_array($action, self::SUPPORTED_ATTRIBUTES, true);
    }

    /**
     * @param string       $action
     * @param Organization $organization
     */
    protected function voteOnAttribute($action, $organization, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if (!$member = $this->memberRepository->findMember($user, $organization)) {
            return false;
        }

        return $member->isAdmin();
    }
}
