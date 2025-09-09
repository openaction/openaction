<?php

namespace App\Community\Consumer;

use App\Community\ContactViewBuilder;
use App\Repository\Community\PhoningCampaignRepository;
use App\Repository\Community\PhoningCampaignTargetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Resolve the filters of a given campaign and create entites for each target.
 */
#[AsMessageHandler]
final class StartPhoningCampaignHandler
{
    private EntityManagerInterface $em;
    private PhoningCampaignRepository $campaignRepository;
    private ContactViewBuilder $contactViewBuilder;
    private PhoningCampaignTargetRepository $targetRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        PhoningCampaignRepository $campaignRepository,
        ContactViewBuilder $contactViewBuilder,
        PhoningCampaignTargetRepository $targetRepository,
        LoggerInterface $logger,
    ) {
        $this->em = $em;
        $this->campaignRepository = $campaignRepository;
        $this->contactViewBuilder = $contactViewBuilder;
        $this->targetRepository = $targetRepository;
        $this->logger = $logger;
    }

    public function __invoke(StartPhoningCampaignMessage $message)
    {
        if (!$campaign = $this->campaignRepository->find($message->getCampaignId())) {
            $this->logger->error('Phoning campaign not found by its ID', ['id' => $message->getCampaignId()]);

            return true;
        }

        if ($campaign->getResolvedAt()) {
            $this->logger->warning('Phoning campaign was already resolved', ['id' => $message->getCampaignId()]);

            return true;
        }

        // Creating database messages
        $this->logger->info('Creating database texting messages', ['id' => $message->getCampaignId()]);

        $this->targetRepository->createCampaignTargets(
            $campaign,
            $this->contactViewBuilder->forPhoningCampaign($campaign)->createQueryBuilder()
        );

        // Marking campaign as resolved
        $this->logger->info('Marking phoning campaign as resolved', ['id' => $message->getCampaignId()]);

        $campaign->markResolved();
        $this->em->persist($campaign);
        $this->em->flush();

        return true;
    }
}
