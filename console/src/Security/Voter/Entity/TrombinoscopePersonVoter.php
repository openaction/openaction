<?php

namespace App\Security\Voter\Entity;

use App\Entity\Website\TrombinoscopePerson;
use App\Platform\Permissions;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TrombinoscopePersonVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
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
        if ($subject->isDraft()) {
            return $this->security->isGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS, $subject->getProject());
        }

        return $this->security->isGranted(Permissions::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED, $subject->getProject());
    }
}
