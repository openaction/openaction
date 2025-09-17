<?php

namespace App\Security\Voter\Entity;

use App\Entity\User;
use App\Entity\Website\Petition;
use App\Platform\Permissions;
use App\Repository\OrganizationMemberRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PetitionVoter extends Voter
{
    public function __construct(
        private readonly OrganizationMemberRepository $memberRepository,
        private readonly Security $security,
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Petition && Permissions::WEBSITE_PETITIONS_MANAGE_ENTITY === $attribute;
    }

    /**
     * @param Petition $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if (!$member = $this->memberRepository->findMember($user, $subject->getProject()->getOrganization())) {
            return false;
        }

        if ($member->isAdmin()) {
            return true;
        }

        // Petitions do not yet enforce category-based permissions at entity level.
        // Restrict based on draft/published status using project permissions.
        if (null === $subject->getPublishedAt()) {
            return $this->security->isGranted(Permissions::WEBSITE_PETITIONS_MANAGE_DRAFTS, $subject->getProject());
        }

        return $this->security->isGranted(Permissions::WEBSITE_PETITIONS_MANAGE_PUBLISHED, $subject->getProject());
    }
}
