<?php

namespace App\Community\Consumer;

use App\Bridge\Mailchimp\MailchimpInterface;
use App\Community\ContactViewBuilder;
use App\Community\SendgridMailFactory;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Send a given email campaign through Mailchimp.
 */
#[AsMessageHandler]
final class SendMailchimpEmailingCampaignHandler
{
    private EntityManagerInterface $em;
    private EmailingCampaignRepository $campaignRepository;
    private ContactViewBuilder $contactViewBuilder;
    private EmailingCampaignMessageRepository $messageRepository;
    private SendgridMailFactory $messageFactory;
    private MailchimpInterface $mailchimp;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        EmailingCampaignRepository $campaignRepository,
        ContactViewBuilder $contactViewBuilder,
        EmailingCampaignMessageRepository $messageRepository,
        SendgridMailFactory $messageFactory,
        MailchimpInterface $mailchimp,
        LoggerInterface $logger,
    ) {
        $this->em = $em;
        $this->campaignRepository = $campaignRepository;
        $this->contactViewBuilder = $contactViewBuilder;
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
        $this->mailchimp = $mailchimp;
        $this->logger = $logger;
    }

    public function __invoke(SendMailchimpEmailingCampaignMessage $message)
    {
        if (!$campaign = $this->campaignRepository->find($message->getCampaignId())) {
            $this->logger->error('Campaign not found by its ID', ['id' => $message->getCampaignId()]);

            return true;
        }

        if ($campaign->getResolvedAt()) {
            $this->logger->warning('Campaign was already resolved', ['id' => $message->getCampaignId()]);

            return true;
        }

        // Synchronize campaign contacts with Mailchimp
        $contacts = $this->contactViewBuilder->forEmailingCampaign($campaign)
            ->createQueryBuilder()
            ->select('c.email', 'c.createdAt')
            ->getQuery()
            ->getArrayResult();

        $mailchimpCampaignId = $this->mailchimp->sendCampaign(
            campaign: $campaign,
            htmlContent: $this->messageFactory->createMailchimpCampaignBody($campaign),
            contacts: $contacts,
        );

        // Create sent messages
        $this->messageRepository->createCampaignMessages(
            $campaign,
            $this->contactViewBuilder->forEmailingCampaign($campaign)->createQueryBuilder(),
            sent: true,
        );

        // Marking campaign as resolved and sent
        $this->logger->info('Marking campaign as resolved and sent', ['id' => $message->getCampaignId()]);

        $campaign->markSentExternally($mailchimpCampaignId);
        $this->em->persist($campaign);
        $this->em->flush();

        return true;
    }
}
