<?php

namespace App\Community;

use App\Bridge\Sendgrid\Model\Recipient;
use App\Bridge\Sendgrid\SendgridInterface;
use App\Community\Consumer\SendBrevoEmailingCampaignMessage;
use App\Community\Consumer\SendEmailingCampaignMessage;
use App\Community\Consumer\SendMailchimpEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailingCampaignSender
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepo,
        private readonly ContactViewBuilder $contactViewBuilder,
        private readonly SendgridMailFactory $messageFactory,
        private readonly SendgridInterface $sendgrid,
        private readonly MessageBusInterface $bus,
        private readonly LoggerInterface $logger,
    ) {
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

        $batch = $this->messageFactory->createCampaignBatch($campaign, $recipients, true);
        $this->sendgrid->sendMessage($this->messageFactory->createMailFromBatch($batch));

        return true;
    }

    public function sendAll(EmailingCampaign $campaign): bool
    {
        $recipientsCount = $this->contactViewBuilder->forEmailingCampaign($campaign)->count();
        $organization = $campaign->getProject()->getOrganization();

        if ('brevo' === $organization->getEmailProvider()) {
            if (!$this->isBrevoConfigured($organization)) {
                $this->logger->error('Brevo provider selected but configuration is incomplete', [
                    'organization' => $organization->getId(),
                ]);

                return false;
            }

            if (!$this->organizationRepo->useCredits($organization, $recipientsCount, 'emailing')) {
                return false;
            }

            $this->bus->dispatch(new SendBrevoEmailingCampaignMessage($campaign->getId()));

            return true;
        }

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

    private function isBrevoConfigured(Organization $organization): bool
    {
        return (bool) $organization->getBrevoApiKey()
            && (bool) $organization->getBrevoListId()
            && ($organization->getBrevoSenderId() || $organization->getBrevoSenderEmail());
    }
}
