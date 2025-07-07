<?php

namespace App\Security\Voter\Entity;

use App\Entity\User;
use App\Entity\Website\TrombinoscopePerson;
use App\Platform\Permissions;
use App\Repository\OrganizationMemberRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TrombinoscopePersonVoter extends Voter
{
    public function __construct(
        private readonly OrganizationMemberRepository $memberRepository,
        private readonly Security $security,
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof TrombinoscopePerson
            && Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_ENTITY === $attribute;
    }

    /**
     * @param TrombinoscopePerson $subject
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

        if (!$member->getProjectsPermissions()->hasCategoryPermission(
            projectId: $subject->getProject()->getUuid()->toRfc4122(),
            type: 'trombinoscope',
            entityCategoriesUuids: $subject->getCategories()->map(fn ($c) => $c->getUuid()->toRfc4122())->toArray(),
        )) {
            return false;
        }

        if ($subject->isDraft()) {
            return $this->security->isGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $subject->getProject());
        }

        return $this->security->isGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $subject->getProject());
    }
}
