<?php

namespace App\Community;

use App\Bridge\Sendgrid\Model\Recipient;
use App\Bridge\Sendgrid\SendgridInterface;
use App\Community\Consumer\SendEmailingCampaignMessage;
use App\Community\Consumer\SendMailchimpEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Repository\OrganizationRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailingCampaignSender
{
    private OrganizationRepository $organizationRepo;
    private ContactViewBuilder $contactViewBuilder;
    private SendgridMailFactory $messageFactory;
    private SendgridInterface $sendgrid;
    private MessageBusInterface $bus;

    public function __construct(
        OrganizationRepository $organizationRepo,
        ContactViewBuilder $contactViewBuilder,
        SendgridMailFactory $messageFactory,
        SendgridInterface $sendgrid,
        MessageBusInterface $bus,
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
        $organization = $campaign->getProject()->getOrganization();

        if (!$this->organizationRepo->useCredits($organization, $recipientsCount, 'emailing')) {
            return false;
        }

        if ('mailchimp' === $organization->getEmailProvider() && $organization->getMailchimpApiKey()) {
            $this->bus->dispatch(new SendMailchimpEmailingCampaignMessage($campaign->getId()));

            return true;
        }

        $this->bus->dispatch(new SendEmailingCampaignMessage($campaign->getId()));

        return true;
    }
}
