<?php

namespace App\Community;

use App\Bridge\Brevo\BrevoInterface;
use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Bridge\Sendgrid\Model\Recipient;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Organization;
use App\Repository\Community\EmailAutomationMessageRepository;
use App\Repository\OrganizationRepository;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class EmailAutomationSender
{
    public function __construct(
        private EntityManagerInterface $em,
        private OrganizationRepository $organizationRepository,
        private SendgridMailFactory $messageFactory,
        private BrevoInterface $brevo,
        private EmailAutomationMessageRepository $messageRepository,
        private MessageBusInterface $bus,
    ) {
    }

    public function send(EmailAutomation $automation, ?Contact $contact, array $additionalVariables = []): bool
    {
        if (!$this->organizationRepository->useCredits($automation->getOrganization(), 1, 'automation')) {
            return false;
        }

        // If no automation email and no contact email, ignore
        if (!$automation->getToEmail() && (!$contact || !$contact->getEmail())) {
            return false;
        }

        // Persist the automation message
        if ($automation->getToEmail()) {
            $this->messageRepository->createNotificationMessage($automation);
        } elseif ($contact && $contact->getEmail()) {
            $this->messageRepository->createMessage($automation, $contact);
        }

        // Send the automation
        $recipient = null;
        if ($automation->getToEmail()) {
            $recipient = Recipient::createFromNotification($automation->getToEmail(), Uid::random(), $additionalVariables);
        } elseif ($contact) {
            $recipient = Recipient::createFromContact($contact, Uid::random(), $additionalVariables);
        }

        if ($recipient) {
            $organization = $automation->getOrganization();
            if ($this->isBrevoConfigured($organization)) {
                $customVariables = $recipient->getVariables();
                if ($additionalVariables) {
                    $customVariables = array_merge($customVariables, $additionalVariables);
                }

                $this->brevo->sendTransactionalEmail(
                    apiKey: (string) $organization->getBrevoApiKey(),
                    fromEmail: (string) $organization->getBrevoSenderEmail(),
                    fromName: $organization->getName(),
                    toEmail: $recipient->getEmail(),
                    subject: $automation->getSubject(),
                    htmlContent: $this->messageFactory->createAutomationBody($automation),
                    replyToEmail: $automation->getReplyToEmail(),
                    replyToName: $automation->getReplyToName(),
                    customVariables: $customVariables,
                );

                return true;
            }

            $batch = $this->messageFactory->createAutomationBatch($automation, $recipient);
            $this->em->persist($batch);
            $this->em->flush();

            $this->bus->dispatch(new SendgridMessage($batch->getId()));
        }

        return true;
    }

    private function isBrevoConfigured(Organization $organization): bool
    {
        return 'brevo' === $organization->getEmailProvider()
            && (bool) $organization->getBrevoApiKey()
            && (bool) $organization->getBrevoSenderEmail();
    }
}
