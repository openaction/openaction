<?php

namespace App\Bridge\Integromat;

use App\Entity\Community\Contact;

interface IntegromatInterface
{
    public function triggerWebhooks(Contact $contact);
}
