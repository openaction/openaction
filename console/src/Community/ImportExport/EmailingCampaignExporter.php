<?php

namespace App\Community\ImportExport;

use App\Community\ImportExport\Consumer\ExportEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Entity\User;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailingCampaignExporter
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function requestExport(User $user, EmailingCampaign $campaign)
    {
        $this->bus->dispatch(new ExportEmailingCampaignMessage($user->getLocale(), $user->getEmail(), $campaign->getId()));
    }
}
