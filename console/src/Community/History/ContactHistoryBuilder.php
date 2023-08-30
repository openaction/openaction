<?php

namespace App\Community\History;

use App\Community\History\Model\ContactHistoryItem;
use App\Entity\Community\Contact;

class ContactHistoryBuilder
{
    /**
     * @var ContactHistorySourceInterface[]
     */
    private iterable $sources;

    public function __construct(iterable $sources)
    {
        $this->sources = $sources;
    }

    /**
     * @return ContactHistoryItem[]
     */
    public function buildHistory(Contact $contact): array
    {
        $dates = [];
        $items = [];
        $i = 0;

        foreach ($this->sources as $source) {
            foreach ($source->getItems($contact) as $item) {
                $dates[] = $item->getDate();
                $items[] = $item;

                ++$i;

                if (50 === $i) {
                    break 2;
                }
            }
        }

        array_multisort($dates, SORT_DESC, $items);

        return $items;
    }
}
