<?php

namespace App\Security\Voter\Entity;

use App\Entity\Website\ManifestoProposal;
use App\Entity\Website\ManifestoTopic;
use App\Platform\Permissions;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ManifestoVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    protected function supports($attribute, $subject): bool
    {
        return ($subject instanceof ManifestoTopic || $subject instanceof ManifestoProposal)
            && Permissions::WEBSITE_MANIFESTO_MANAGE_ENTITY === $attribute;
    }

    /**
     * @param ManifestoTopic|ManifestoProposal $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $topic = $subject instanceof ManifestoProposal ? $subject->getTopic() : $subject;

        if ($topic->isDraft()) {
            return $this->security->isGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_DRAFTS, $topic->getProject());
        }

        return $this->security->isGranted(Permissions::WEBSITE_MANIFESTO_MANAGE_PUBLISHED, $topic->getProject());
    }
}
