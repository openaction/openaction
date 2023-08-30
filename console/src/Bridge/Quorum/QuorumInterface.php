<?php

namespace App\Bridge\Quorum;

use App\Entity\Community\Contact;

interface QuorumInterface
{
    public function persist(Contact $contact);
}
