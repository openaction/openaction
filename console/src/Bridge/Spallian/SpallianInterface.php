<?php

namespace App\Bridge\Spallian;

use App\Entity\Community\Contact;

interface SpallianInterface
{
    public function persist(Contact $contact, bool $isCreation);
}
