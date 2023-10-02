<?php

namespace App\Security\Voter\Entity;

use App\Entity\Website\Post;
use App\Platform\Permissions;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Post && Permissions::WEBSITE_POSTS_MANAGE_ENTITY === $attribute;
    }

    /**
     * @param Post $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($subject->isDraft()) {
            return $this->security->isGranted(Permissions::WEBSITE_POSTS_MANAGE_DRAFTS, $subject->getProject());
        }

        return $this->security->isGranted(Permissions::WEBSITE_POSTS_MANAGE_PUBLISHED, $subject->getProject());
    }
}
