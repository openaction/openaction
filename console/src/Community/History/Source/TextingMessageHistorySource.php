<?php

namespace App\Community\History\Source;

use App\Community\History\ContactHistorySourceInterface;
use App\Community\History\Model\ContactHistoryItem;
use App\Entity\Community\Contact;
use App\Repository\Community\TextingCampaignMessageRepository;

class TextingMessageHistorySource implements ContactHistorySourceInterface
{
    private TextingCampaignMessageRepository $repository;

    public function __construct(TextingCampaignMessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getItems(Contact $contact): iterable
    {
        foreach ($this->repository->findContactHistory($contact) as $message) {
            if ($message->isSent()) {
                yield new ContactHistoryItem($message->getSentAt(), 'texting', $message);
            }
        }
    }
}
