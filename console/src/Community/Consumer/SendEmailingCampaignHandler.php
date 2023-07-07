<?php

namespace App\Community\Consumer;

use App\Community\ContactViewBuilder;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Resolve the filters of a given campaign, create entites for each message
 * and send the batch messages through a different consumer.
 */
final class SendEmailingCampaignHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private EmailingCampaignRepository $campaignRepository;
    private ContactViewBuilder $contactViewBuilder;
    private EmailingCampaignMessageRepository $messageRepository;
    private MessageBusInterface $bus;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        EmailingCampaignRepository $campaignRepository,
        ContactViewBuilder $contactViewBuilder,
        EmailingCampaignMessageRepository $messageRepository,
        MessageBusInterface $bus,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->campaignRepository = $campaignRepository;
        $this->contactViewBuilder = $contactViewBuilder;
        $this->messageRepository = $messageRepository;
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function __invoke(SendEmailingCampaignMessage $message)
    {
        if (!$campaign = $this->campaignRepository->find($message->getCampaignId())) {
            $this->logger->error('Campaign not found by its ID', ['id' => $message->getCampaignId()]);

            return true;
        }

        if ($campaign->getResolvedAt()) {
            $this->logger->warning('Campaign was already resolved', ['id' => $message->getCampaignId()]);

            return true;
        }

        // Creating database messages
        $this->logger->info('Creating database messages', ['id' => $message->getCampaignId()]);

        $this->messageRepository->createCampaignMessages(
            $campaign,
            $this->contactViewBuilder->forEmailingCampaign($campaign)->createQueryBuilder()
        );

        // Marking campaign as resolved
        $this->logger->info('Marking campaign as resolved', ['id' => $message->getCampaignId()]);

        $campaign->markResolved();
        $this->em->persist($campaign);
        $this->em->flush();

        // Dispatching batches creation
        $this->logger->info('Dispatching batches creation', ['id' => $message->getCampaignId()]);

        $this->bus->dispatch(new CreateEmailingCampaignBatchesMessage($campaign->getId()));

        return true;
    }
}
