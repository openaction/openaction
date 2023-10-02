<?php

namespace App\Security\Voter\Entity;

use App\Entity\Website\Event;
use App\Platform\Permissions;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Event && Permissions::WEBSITE_EVENTS_MANAGE_ENTITY === $attribute;
    }

    /**
     * @param Event $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($subject->isDraft()) {
            return $this->security->isGranted(Permissions::WEBSITE_EVENTS_MANAGE_DRAFTS, $subject->getProject());
        }

        return $this->security->isGranted(Permissions::WEBSITE_EVENTS_MANAGE_PUBLISHED, $subject->getProject());
    }
}
