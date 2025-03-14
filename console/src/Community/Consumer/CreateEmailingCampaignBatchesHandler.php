<?php

namespace App\Community\Consumer;

use App\Bridge\Postmark\Consumer\PostmarkMessage;
use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Bridge\Sendgrid\Model\Recipient;
use App\Community\PostmarkMailFactory;
use App\Community\SendgridMailFactory;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use App\Util\PhoneNumber;
use App\Util\Uid;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

/*
 * Resolve the filters of a given campaign, create entites for each message
 * and send the batch messages through a different consumer.
 */
final class CreateEmailingCampaignBatchesHandler implements MessageHandlerInterface
{
    private EmailingCampaignRepository $campaignRepository;
    private EmailingCampaignMessageRepository $messageRepository;
    private SendgridMailFactory $sendgridMailFactory;
    private PostmarkMailFactory $postmarkMailFactory;
    private MessageBusInterface $bus;
    private LoggerInterface $logger;

    public function __construct(
        EmailingCampaignRepository        $campaignRepository,
        EmailingCampaignMessageRepository $messageRepository,
        SendgridMailFactory               $sendgridMailFactory,
        PostmarkMailFactory               $postmarkMailFactory,
        MessageBusInterface               $bus,
        LoggerInterface                   $logger
    ) {
        $this->campaignRepository = $campaignRepository;
        $this->messageRepository = $messageRepository;
        $this->sendgridMailFactory = $sendgridMailFactory;
        $this->postmarkMailFactory = $postmarkMailFactory;
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function __invoke(CreateEmailingCampaignBatchesMessage $message)
    {
        if (!$campaign = $this->campaignRepository->find($message->getCampaignId())) {
            $this->logger->error('Campaign not found by its ID', ['id' => $message->getCampaignId()]);

            return true;
        }

        $provider = $campaign->getProject()->getOrganization()->getEmailProvider();
        $batches = $this->messageRepository->buildMessagesBatches($campaign);

        foreach ($batches as $batch) {
            $recipients = [];

            foreach ($batch as $mailMessage) {
                // Ignore invalid emails
                if (false === filter_var($mailMessage['email'], FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) {
                    $this->logger->error('Invalid email ignored when sending campaign', [
                        'project' => $campaign->getProject()->getId(),
                        'project_name' => $campaign->getProject()->getName(),
                        'email' => $mailMessage['email'],
                    ]);

                    continue;
                }

                $recipients[] = new Recipient($mailMessage['email'], $mailMessage['id'], [
                    '-contact-id-' => Uid::toBase62(Uuid::fromString($mailMessage['uuid'])),
                    '-contact-email-' => $mailMessage['email'],
                    '-contact-phone-' => PhoneNumber::format($mailMessage['parsedContactPhone']),
                    '-contact-formal-title-' => $mailMessage['profileFormalTitle'],
                    '-contact-firstname-' => $mailMessage['profileFirstName'],
                    '-contact-lastname-' => $mailMessage['profileLastName'],
                    '-contact-fullname-' => $mailMessage['profileFirstName'].' '.$mailMessage['profileLastName'],
                    '-contact-gender-' => $mailMessage['profileGender'],
                    '-contact-nationality-' => $mailMessage['profileNationality'],
                    '-contact-company-' => $mailMessage['profileCompany'],
                    '-contact-job-title-' => $mailMessage['profileJobTitle'],
                    '-contact-streetline-1-' => $mailMessage['addressStreetLine1'],
                    '-contact-streetline-2-' => $mailMessage['addressStreetLine2'],
                    '-contact-zipcode-' => $mailMessage['addressZipCode'],
                    '-contact-city-' => $mailMessage['addressCity'],
                    '-contact-country-' => $mailMessage['addressCountry'],
                ]);
            }

            if ('postmark' === $provider) {
                $this->bus->dispatch(new PostmarkMessage($this->postmarkMailFactory->createEmailing($campaign, $recipients)));
            } else {
                $this->bus->dispatch(new SendgridMessage($this->sendgridMailFactory->createEmailing($campaign, $recipients)));
            }
        }

        return true;
    }
}
