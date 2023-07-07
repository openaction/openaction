<?php

namespace App\Community\Consumer;

use App\Community\ContactViewBuilder;
use App\Repository\Community\TextingCampaignMessageRepository;
use App\Repository\Community\TextingCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Resolve the filters of a given campaign, create entites for each message
 * and send the batch messages through a different consumer.
 */
final class SendTextingCampaignHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private TextingCampaignRepository $campaignRepository;
    private ContactViewBuilder $contactViewBuilder;
    private TextingCampaignMessageRepository $messageRepository;
    private MessageBusInterface $bus;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        TextingCampaignRepository $campaignRepository,
        ContactViewBuilder $contactViewBuilder,
        TextingCampaignMessageRepository $messageRepository,
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

    public function __invoke(SendTextingCampaignMessage $message)
    {
        if (!$campaign = $this->campaignRepository->find($message->getCampaignId())) {
            $this->logger->error('Texting campaign not found by its ID', ['id' => $message->getCampaignId()]);

            return true;
        }

        if ($campaign->getResolvedAt()) {
            $this->logger->warning('Texting campaign was already resolved', ['id' => $message->getCampaignId()]);

            return true;
        }

        // Creating database messages
        $this->logger->info('Creating database texting messages', ['id' => $message->getCampaignId()]);

        $this->messageRepository->createCampaignMessages(
            $campaign,
            $this->contactViewBuilder->forTextingCampaign($campaign)->createQueryBuilder()
        );

        // Marking campaign as resolved
        $this->logger->info('Marking texting campaign as resolved', ['id' => $message->getCampaignId()]);

        $campaign->markResolved();
        $this->em->persist($campaign);
        $this->em->flush();

        // Dispatching batches creation
        $this->logger->info('Dispatching batches creation for texting campaign', ['id' => $message->getCampaignId()]);

        $this->bus->dispatch(new CreateTextingCampaignBatchesMessage($campaign->getId()));

        return true;
    }
}
