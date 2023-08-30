<?php

namespace App\Community\Automation\EmailTrigger;

use App\Entity\Community\Contact;
use App\Entity\Organization;

interface EmailTriggerInterface
{
    public function getTrigger(): string;

    public function handle(Organization $organization, ?Contact $contact, $subject);
}
