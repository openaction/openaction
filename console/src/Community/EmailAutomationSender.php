<?php

namespace App\Community;

use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Bridge\Sendgrid\Model\Recipient;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Repository\Community\EmailAutomationMessageRepository;
use App\Repository\OrganizationRepository;
use App\Util\Uid;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailAutomationSender
{
    private OrganizationRepository $organizationRepo;
    private SendgridMailFactory $messageFactory;
    private EmailAutomationMessageRepository $messageRepository;
    private MessageBusInterface $bus;

    public function __construct(
        OrganizationRepository           $organizationRepo,
        SendgridMailFactory              $messageFactory,
        EmailAutomationMessageRepository $messageRepository,
        MessageBusInterface              $bus
    ) {
        $this->organizationRepo = $organizationRepo;
        $this->messageFactory = $messageFactory;
        $this->messageRepository = $messageRepository;
        $this->bus = $bus;
    }

    public function send(EmailAutomation $automation, ?Contact $contact, array $additionalVariables = []): bool
    {
        if (!$this->organizationRepo->useCredits($automation->getOrganization(), 1, 'automation')) {
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
            $this->bus->dispatch(
                new SendgridMessage($this->messageFactory->createAutomationEmail($automation, $recipient))
            );
        }

        return true;
    }
}
