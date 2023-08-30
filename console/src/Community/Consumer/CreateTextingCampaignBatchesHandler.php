<?php

namespace App\Community\Consumer;

use App\Bridge\Twilio\Model\Recipient;
use App\Community\TextingMessageFactory;
use App\Repository\Community\TextingCampaignMessageRepository;
use App\Repository\Community\TextingCampaignRepository;
use App\Util\PhoneNumber;
use App\Util\Uid;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

/*
 * Resolve the filters of a given campaign, create entities for each message
 * and send the batch messages through a different consumer.
 */
final class CreateTextingCampaignBatchesHandler implements MessageHandlerInterface
{
    private TextingCampaignRepository $campaignRepository;
    private TextingCampaignMessageRepository $messageRepository;
    private TextingMessageFactory $messageFactory;
    private MessageBusInterface $bus;
    private LoggerInterface $logger;

    public function __construct(
        TextingCampaignRepository $campaignRepository,
        TextingCampaignMessageRepository $messageRepository,
        TextingMessageFactory $messageFactory,
        MessageBusInterface $bus,
        LoggerInterface $logger
    ) {
        $this->campaignRepository = $campaignRepository;
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function __invoke(CreateTextingCampaignBatchesMessage $message)
    {
        if (!$campaign = $this->campaignRepository->find($message->getCampaignId())) {
            $this->logger->error('Texting campaign not found by its ID', ['id' => $message->getCampaignId()]);

            return true;
        }

        $batches = $this->messageRepository->buildMessagesBatches($campaign);

        foreach ($batches as $batch) {
            $recipients = [];
            foreach ($batch as $textMessage) {
                $recipients[] = new Recipient($textMessage['parsedContactPhone'], $textMessage['id'], [
                    '-contact-id-' => Uid::toBase62(Uuid::fromString($textMessage['uuid'])),
                    '-contact-email-' => $textMessage['email'],
                    '-contact-phone-' => PhoneNumber::format($textMessage['parsedContactPhone']),
                    '-contact-formal-title-' => $textMessage['profileFormalTitle'],
                    '-contact-firstname-' => $textMessage['profileFirstName'],
                    '-contact-lastname-' => $textMessage['profileLastName'],
                    '-contact-fullname-' => $textMessage['profileFirstName'].' '.$textMessage['profileLastName'],
                    '-contact-gender-' => $textMessage['profileGender'],
                    '-contact-nationality-' => $textMessage['profileNationality'],
                    '-contact-company-' => $textMessage['profileCompany'],
                    '-contact-job-title-' => $textMessage['profileJobTitle'],
                    '-contact-streetline-1-' => $textMessage['addressStreetLine1'],
                    '-contact-streetline-2-' => $textMessage['addressStreetLine2'],
                    '-contact-zipcode-' => $textMessage['addressZipCode'],
                    '-contact-city-' => $textMessage['addressCity'],
                    '-contact-country-' => $textMessage['addressCountry'],
                ]);
            }

            $this->bus->dispatch($this->messageFactory->createMessage($campaign, $recipients));
        }

        return true;
    }
}
