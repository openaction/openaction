<?php

namespace App\Community\History\Source;

use App\Community\History\ContactHistorySourceInterface;
use App\Community\History\Model\ContactHistoryItem;
use App\Entity\Community\Contact;
use App\Repository\Website\FormAnswerRepository;

class FormAnswerHistorySource implements ContactHistorySourceInterface
{
    private FormAnswerRepository $repository;

    public function __construct(FormAnswerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getItems(Contact $contact): iterable
    {
        foreach ($this->repository->findContactHistory($contact) as $answer) {
            yield new ContactHistoryItem($answer->getCreatedAt(), 'form_answer', $answer);
        }
    }
}
