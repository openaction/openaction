<?php

namespace App\Community\History\Source;

use App\Community\History\ContactHistorySourceInterface;
use App\Community\History\Model\ContactHistoryItem;
use App\Entity\Community\Contact;
use App\Repository\Community\EmailingCampaignMessageRepository;

class EmailingMessageHistorySource implements ContactHistorySourceInterface
{
    private EmailingCampaignMessageRepository $repository;

    public function __construct(EmailingCampaignMessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getItems(Contact $contact): iterable
    {
        foreach ($this->repository->findContactHistory($contact) as $message) {
            if ($message->isSent()) {
                yield new ContactHistoryItem($message->getSentAt(), 'emailing', $message);
            }
        }
    }
}
