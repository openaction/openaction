<?php

namespace App\Community;

use App\Bridge\Brevo\BrevoInterface;
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
        private readonly BrevoInterface $brevo,
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

        $organization = $campaign->getProject()->getOrganization();

        if (!$this->organizationRepo->useCredits($organization, count($recipients), 'emailing_preview')) {
            return false;
        }

        if ('brevo' === $organization->getEmailProvider() && $this->isBrevoConfigured($organization)) {
            $htmlContent = $this->messageFactory->createBrevoCampaignBody($campaign, true);

            foreach ($recipients as $recipient) {
                $this->brevo->sendTransactionalEmail(
                    apiKey: (string) $organization->getBrevoApiKey(),
                    fromEmail: (string) $organization->getBrevoSenderEmail(),
                    fromName: $organization->getName(),
                    toEmail: $recipient->getEmail(),
                    subject: 'Test - '.$campaign->getSubject(),
                    htmlContent: $htmlContent,
                    replyToEmail: $campaign->getReplyToEmail() ?: null,
                    replyToName: $campaign->getReplyToName() ?: null,
                );
            }

            return true;
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
            && (bool) $organization->getBrevoSenderEmail();
    }
}
