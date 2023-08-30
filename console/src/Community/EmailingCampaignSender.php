<?php

namespace App\Community;

use App\Bridge\Sendgrid\Model\Recipient;
use App\Bridge\Sendgrid\SendgridInterface;
use App\Community\Consumer\SendEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Repository\OrganizationRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailingCampaignSender
{
    private OrganizationRepository $organizationRepo;
    private ContactViewBuilder $contactViewBuilder;
    private EmailMessageFactory $messageFactory;
    private SendgridInterface $sendgrid;
    private MessageBusInterface $bus;

    public function __construct(
        OrganizationRepository $organizationRepo,
        ContactViewBuilder $contactViewBuilder,
        EmailMessageFactory $messageFactory,
        SendgridInterface $sendgrid,
        MessageBusInterface $bus
    ) {
        $this->organizationRepo = $organizationRepo;
        $this->contactViewBuilder = $contactViewBuilder;
        $this->messageFactory = $messageFactory;
        $this->sendgrid = $sendgrid;
        $this->bus = $bus;
    }

    public function sendPreview(EmailingCampaign $campaign, array $to): bool
    {
        $recipients = [];
        foreach ($to as $email) {
            $recipients[] = new Recipient($email);
        }

        if (!$this->organizationRepo->useCredits($campaign->getProject()->getOrganization(), count($recipients), 'emailing_preview')) {
            return false;
        }

        $this->sendgrid->sendMessage($this->messageFactory->createEmailing($campaign, $recipients, true));

        return true;
    }

    public function sendAll(EmailingCampaign $campaign): bool
    {
        $recipientsCount = $this->contactViewBuilder->forEmailingCampaign($campaign)->count();

        if (!$this->organizationRepo->useCredits($campaign->getProject()->getOrganization(), $recipientsCount, 'emailing')) {
            return false;
        }

        $this->bus->dispatch(new SendEmailingCampaignMessage($campaign->getId()));

        return true;
    }
}
