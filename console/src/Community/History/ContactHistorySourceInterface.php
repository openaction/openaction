<?php

namespace App\Community\History;

use App\Community\History\Model\ContactHistoryItem;
use App\Entity\Community\Contact;

interface ContactHistorySourceInterface
{
    /**
     * @return ContactHistoryItem[]|iterable
     */
    public function getItems(Contact $contact): iterable;
}
