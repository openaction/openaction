<?php

namespace App\Community\Automation\EmailTrigger;

use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Community\Tag;
use App\Entity\Organization;

class ContactTaggedEmailTrigger extends AbstractEmailTrigger
{
    public function getTrigger(): string
    {
        return EmailAutomation::TRIGGER_CONTACT_TAGGED;
    }

    /**
     * @param Tag $subject
     */
    public function handle(Organization $organization, ?Contact $contact, $subject)
    {
        if (!$contact || !$subject instanceof Tag) {
            return;
        }

        $automations = $this->findMatchingAutomationsFor(EmailAutomation::TRIGGER_CONTACT_TAGGED, $organization, $contact);

        foreach ($automations as $automation) {
            if ($automation->getTagFilter()?->getId() === $subject->getId()) {
                $this->sendAutomationEmail($automation, $contact);
            }
        }
    }
}
