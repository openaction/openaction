<?php

namespace App\Community\Automation;

use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;

class EmailAutomationMatcher
{
    public function matches(EmailAutomation $automation, ?Contact $contact): bool
    {
        // Check whether it can be ran
        if (!$contact && !$automation->getToEmail()) {
            return false;
        }

        // Check type
        if (EmailAutomation::TYPE_MEMBER === $automation->getTypeFilter() && !$contact->isMember()) {
            return false;
        }

        if (EmailAutomation::TYPE_CONTACT === $automation->getTypeFilter() && $contact->isMember()) {
            return false;
        }

        // Check area
        if ($automation->getAreaFilter() && !$automation->getAreaFilter()->contains($contact->getArea())) {
            return false;
        }

        // Check tag
        if ($automation->getTagFilter() && !$contact->hasMetadataTag($automation->getTagFilter())) {
            return false;
        }

        return true;
    }
}
