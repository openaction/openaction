<?php

namespace App\Community\Consumer;

use App\Bridge\Brevo\BrevoInterface;
use App\Community\ContactViewBuilder;
use App\Community\SendgridMailFactory;
use App\Entity\Organization;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Send a given email campaign through Brevo.
 */
final class SendBrevoEmailingCampaignHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EmailingCampaignRepository $campaignRepository,
        private readonly ContactViewBuilder $contactViewBuilder,
        private readonly EmailingCampaignMessageRepository $messageRepository,
        private readonly SendgridMailFactory $messageFactory,
        private readonly BrevoInterface $brevo,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendBrevoEmailingCampaignMessage $message)
    {
        if (!$campaign = $this->campaignRepository->find($message->getCampaignId())) {
            $this->logger->error('Campaign not found by its ID', ['id' => $message->getCampaignId()]);

            return true;
        }

        if ($campaign->getResolvedAt()) {
            $this->logger->warning('Campaign was already resolved', ['id' => $message->getCampaignId()]);

            return true;
        }

        $organization = $campaign->getProject()->getOrganization();
        if (!$this->isConfigured($organization)) {
            $this->logger->error('Brevo provider selected but configuration is incomplete', [
                'id' => $message->getCampaignId(),
            ]);

            return true;
        }

        $contacts = $this->contactViewBuilder->forEmailingCampaign($campaign)
            ->createQueryBuilder()
            ->select('c.email', 'c.createdAt')
            ->getQuery()
            ->getArrayResult();

        $brevoCampaignId = $this->brevo->sendCampaign(
            campaign: $campaign,
            htmlContent: $this->messageFactory->createBrevoCampaignBody($campaign),
            contacts: $contacts,
        );

        $this->messageRepository->createCampaignMessages(
            $campaign,
            $this->contactViewBuilder->forEmailingCampaign($campaign)->createQueryBuilder(),
            sent: true,
        );

        $this->logger->info('Marking Brevo campaign as resolved and sent', ['id' => $message->getCampaignId()]);

        $campaign->markSentExternally($brevoCampaignId);
        $this->em->persist($campaign);
        $this->em->flush();

        return true;
    }

    private function isConfigured(Organization $organization): bool
    {
        return (bool) $organization->getBrevoApiKey()
            && (bool) $organization->getBrevoListId()
            && ($organization->getBrevoSenderId() || $organization->getBrevoSenderEmail());
    }
}
