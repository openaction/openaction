<?php

namespace App\Community\ImportExport;

use App\Community\ImportExport\Consumer\ExportMessage;
use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\Messenger\MessageBusInterface;

class ContactExporter
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function requestExport(User $user, Organization $organization, int $tagId = null)
    {
        $this->bus->dispatch(new ExportMessage($user->getLocale(), $user->getEmail(), $organization->getId(), $tagId));
    }
}
