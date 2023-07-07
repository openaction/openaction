<?php

namespace App\Community\Automation\EmailTrigger;

use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Organization;

class ContactCreatedEmailTrigger extends AbstractEmailTrigger
{
    public function getTrigger(): string
    {
        return EmailAutomation::TRIGGER_NEW_CONTACT;
    }

    /**
     * @param null $subject
     */
    public function handle(Organization $organization, ?Contact $contact, $subject)
    {
        if (!$contact) {
            return;
        }

        $automations = $this->findMatchingAutomationsFor(EmailAutomation::TRIGGER_NEW_CONTACT, $organization, $contact);

        foreach ($automations as $automation) {
            $this->sendAutomationEmail($automation, $contact);
        }
    }
}
