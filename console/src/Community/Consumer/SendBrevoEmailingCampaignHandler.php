<?php

namespace App\Community\Consumer;

use App\Bridge\Brevo\BrevoInterface;
use App\Community\ContactViewBuilder;
use App\Community\SendgridMailFactory;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Send a given email campaign through Brevo.
 */
final class SendBrevoEmailingCampaignHandler implements MessageHandlerInterface
{
    private const ADVISORY_LOCK_NAMESPACE = 643;

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
        $connection = $this->em->getConnection();
        $lockAcquired = false;
        $campaign = null;
        $brevoCampaignId = null;
        $sendToken = null;
        $externalEffectStarted = false;

        try {
            $this->acquireCampaignLock($connection, $message->getCampaignId());
            $lockAcquired = true;

            if (!$campaign = $this->campaignRepository->find($message->getCampaignId())) {
                $this->logger->error('Campaign not found by its ID', $this->buildLogContext(
                    message: $message,
                    campaignId: $message->getCampaignId(),
                    state: EmailingCampaign::BREVO_SEND_STATE_FAILED,
                ));

                return true;
            }

            $sendToken = $this->resolveSendToken($campaign, $message);
            if (null === $sendToken) {
                $this->logger->warning('Ignoring Brevo campaign message without send token and without recoverable campaign token', $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $campaign->getExternalId(),
                    state: $campaign->getBrevoSendState(),
                ));

                return true;
            }

            if (!$campaign->hasMatchingSendToken($sendToken)) {
                $this->logger->warning('Ignoring Brevo campaign message with stale token', $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $campaign->getExternalId(),
                    sendToken: $sendToken,
                ));

                return true;
            }

            if ($campaign->isBrevoSendSentState()) {
                $this->logger->warning('Brevo campaign already sent, skipping duplicate handling', $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $campaign->getExternalId(),
                ));

                return true;
            }

            if ($campaign->isBrevoSendFailedState()) {
                $this->logger->warning('Brevo campaign is in failed state, skipping automatic resend', $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $campaign->getExternalId(),
                    sendToken: $sendToken,
                ));

                return true;
            }

            $organization = $campaign->getProject()->getOrganization();
            if (!$this->isConfigured($organization)) {
                $campaign->markBrevoSendFailed();
                $this->em->persist($campaign);
                $this->em->flush();

                $this->logger->error('Brevo provider selected but configuration is incomplete', $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $campaign->getExternalId(),
                    sendToken: $sendToken,
                ));

                return true;
            }

            if (!$campaign->isBrevoSendSendingState()) {
                $campaign->markBrevoSendSending();
                $this->em->persist($campaign);
                $this->em->flush();
            }

            $this->logger->info('Handling Brevo campaign send message', $this->buildLogContext(
                message: $message,
                campaign: $campaign,
                externalId: $campaign->getExternalId(),
                sendToken: $sendToken,
            ));

            $htmlContent = $this->messageFactory->createBrevoCampaignBody($campaign);
            $contactsQueryBuilder = $this->contactViewBuilder->forEmailingCampaign($campaign)->createQueryBuilder();
            $contacts = $this->createContactsFromQueryBuilder($contactsQueryBuilder);

            $brevoListId = $campaign->getExternalListId();
            if (null === $brevoListId) {
                $brevoListId = $this->brevo->createCampaignList($campaign);
                $campaign->setExternalListId($brevoListId);
                $this->em->persist($campaign);
                $this->em->flush();
            }

            $this->brevo->syncCampaignContacts($campaign, $brevoListId, $contacts);

            $brevoCampaignId = $this->normalizeBrevoCampaignId($campaign->getExternalId());

            if (null === $campaign->getBrevoRemoteCreatedAt()) {
                $externalEffectStarted = true;

                if (null === $brevoCampaignId) {
                    $brevoCampaignId = $this->normalizeBrevoCampaignId($this->brevo->createEmailCampaign(
                        campaign: $campaign,
                        htmlContent: $htmlContent,
                        listId: $brevoListId,
                    ));
                }

                if (null === $brevoCampaignId) {
                    throw new \RuntimeException('Brevo error: campaign could not be created.');
                }

                $campaign->markBrevoRemoteCreated($brevoCampaignId);
                $this->em->persist($campaign);
                $this->em->flush();

                $this->logger->info('Brevo remote campaign creation checkpoint persisted', $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $brevoCampaignId,
                    sendToken: $sendToken,
                ));
            }

            if (null === $brevoCampaignId) {
                $brevoCampaignId = $this->normalizeBrevoCampaignId($campaign->getExternalId());
            }

            if (null === $brevoCampaignId) {
                throw new \RuntimeException('Brevo error: campaign external ID is missing after creation checkpoint.');
            }

            if (null === $campaign->getBrevoRemoteSentAt()) {
                $externalEffectStarted = true;

                if (!$this->brevo->isEmailCampaignSent($campaign, $brevoCampaignId)) {
                    $this->brevo->sendEmailCampaignNow($campaign, $brevoCampaignId);
                }

                $campaign->markBrevoRemoteSent();
                $this->em->persist($campaign);
                $this->em->flush();

                $this->logger->info('Brevo remote campaign send checkpoint persisted', $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $brevoCampaignId,
                    sendToken: $sendToken,
                ));
            }

            $this->messageRepository->createCampaignMessages(
                $campaign,
                $contactsQueryBuilder,
                sent: true,
            );

            $campaign->markBrevoSendSent($brevoCampaignId);
            $this->em->persist($campaign);
            $this->em->flush();

            $this->logger->info('Brevo campaign marked as sent', $this->buildLogContext(
                message: $message,
                campaign: $campaign,
                externalId: $brevoCampaignId,
                sendToken: $sendToken,
            ));

            return true;
        } catch (\Throwable $exception) {
            if ($campaign instanceof EmailingCampaign && $externalEffectStarted && null !== $sendToken) {
                $this->markCampaignAsFailed($campaign, $message, $sendToken, $brevoCampaignId, $exception);

                return true;
            }

            $this->logger->warning('Brevo campaign send failed before external effect, letting messenger retry', array_merge(
                $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    campaignId: $message->getCampaignId(),
                    externalId: $brevoCampaignId,
                    state: $campaign?->getBrevoSendState() ?? EmailingCampaign::BREVO_SEND_STATE_QUEUED,
                    sendToken: $sendToken,
                ),
                ['exception' => $exception],
            ));

            throw $exception;
        } finally {
            if ($lockAcquired) {
                $this->releaseCampaignLock($connection, $message->getCampaignId());
            }
        }
    }

    private function isConfigured(Organization $organization): bool
    {
        return (bool) $organization->getBrevoApiKey()
            && (bool) $organization->getBrevoSenderEmail();
    }

    private function normalizeBrevoCampaignId(?string $campaignId): ?string
    {
        $campaignId = null !== $campaignId ? trim($campaignId) : null;

        return '' === $campaignId ? null : $campaignId;
    }

    private function normalizeBrevoContactData(array $contact): array
    {
        $firstName = trim((string) ($contact['firstName'] ?? '')) ?: null;
        $lastName = trim((string) ($contact['lastName'] ?? '')) ?: null;

        return [
            'email' => $contact['email'] ?? null,
            'phone' => $contact['phone'] ?? null,
            'formalTitle' => $contact['formalTitle'] ?? null,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'fullName' => trim(implode(' ', array_filter([$firstName, $lastName]))) ?: null,
            'gender' => $contact['gender'] ?? null,
            'nationality' => $contact['nationality'] ?? null,
            'company' => $contact['company'] ?? null,
            'jobTitle' => $contact['jobTitle'] ?? null,
            'addressLine1' => $contact['addressLine1'] ?? null,
            'addressLine2' => $contact['addressLine2'] ?? null,
            'postalCode' => $contact['postalCode'] ?? null,
            'city' => $contact['city'] ?? null,
            'country' => $contact['countryCode'] ?? null,
        ];
    }

    private function createContactsFromQueryBuilder(QueryBuilder $contactsQueryBuilder): array
    {
        $contacts = (clone $contactsQueryBuilder)
            ->leftJoin('c.addressCountry', 'country')
            ->select(
                'c.email AS email',
                'c.contactPhone AS phone',
                'c.profileFormalTitle AS formalTitle',
                'c.profileFirstName AS firstName',
                'c.profileLastName AS lastName',
                'c.profileGender AS gender',
                'c.profileNationality AS nationality',
                'c.profileCompany AS company',
                'c.profileJobTitle AS jobTitle',
                'c.addressStreetLine1 AS addressLine1',
                'c.addressStreetLine2 AS addressLine2',
                'c.addressZipCode AS postalCode',
                'c.addressCity AS city',
                'country.code AS countryCode',
            )
            ->getQuery()
            ->getArrayResult()
        ;

        return array_map(fn (array $contact) => $this->normalizeBrevoContactData($contact), $contacts);
    }

    private function markCampaignAsFailed(
        EmailingCampaign $campaign,
        SendBrevoEmailingCampaignMessage $message,
        string $sendToken,
        ?string $externalId,
        \Throwable $exception,
    ): void {
        try {
            if ($campaign->hasMatchingSendToken($sendToken) && !$campaign->isBrevoSendSentState()) {
                $campaign->markBrevoSendFailed();
                $this->em->persist($campaign);
                $this->em->flush();
            }

            $this->logger->error('Brevo campaign send failed after external effect, campaign moved to failed state', array_merge(
                $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $externalId,
                    state: EmailingCampaign::BREVO_SEND_STATE_FAILED,
                    sendToken: $sendToken,
                ),
                ['exception' => $exception],
            ));
        } catch (\Throwable $markFailedException) {
            $this->logger->error('Brevo campaign send failed and failed-state persistence also failed', array_merge(
                $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $externalId,
                    state: EmailingCampaign::BREVO_SEND_STATE_FAILED,
                    sendToken: $sendToken,
                ),
                [
                    'exception' => $exception,
                    'markFailedException' => $markFailedException,
                ],
            ));
        }
    }

    private function acquireCampaignLock(Connection $connection, int $campaignId): void
    {
        if ('postgresql' !== $connection->getDatabasePlatform()->getName()) {
            return;
        }

        $connection->executeStatement(
            'SELECT pg_advisory_lock(:namespace, :campaign_id)',
            [
                'namespace' => self::ADVISORY_LOCK_NAMESPACE,
                'campaign_id' => $campaignId,
            ],
        );
    }

    private function releaseCampaignLock(Connection $connection, int $campaignId): void
    {
        if ('postgresql' !== $connection->getDatabasePlatform()->getName()) {
            return;
        }

        try {
            $connection->executeStatement(
                'SELECT pg_advisory_unlock(:namespace, :campaign_id)',
                [
                    'namespace' => self::ADVISORY_LOCK_NAMESPACE,
                    'campaign_id' => $campaignId,
                ],
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Unable to release Brevo campaign advisory lock', [
                'campaignId' => $campaignId,
                'hostname' => gethostname() ?: null,
                'exception' => $exception,
            ]);
        }
    }

    private function resolveSendToken(
        EmailingCampaign $campaign,
        SendBrevoEmailingCampaignMessage $message,
    ): ?string {
        if ($messageSendToken = $message->getSendToken()) {
            return $messageSendToken;
        }

        if ($campaign->isBrevoSendDraftState()) {
            if ($claimedToken = $this->campaignRepository->claimBrevoSend($campaign)) {
                $this->em->refresh($campaign);

                $this->logger->warning('Recovered legacy Brevo campaign message without send token by claiming campaign', $this->buildLogContext(
                    message: $message,
                    campaign: $campaign,
                    externalId: $campaign->getExternalId(),
                    state: $campaign->getBrevoSendState(),
                    sendToken: $claimedToken,
                ));

                return $claimedToken;
            }
        }

        if ($campaignSendToken = $campaign->getSendToken()) {
            $this->logger->warning('Recovered legacy Brevo campaign message without send token from campaign token', $this->buildLogContext(
                message: $message,
                campaign: $campaign,
                externalId: $campaign->getExternalId(),
                state: $campaign->getBrevoSendState(),
                sendToken: $campaignSendToken,
            ));

            return $campaignSendToken;
        }

        return null;
    }

    private function buildLogContext(
        SendBrevoEmailingCampaignMessage $message,
        ?EmailingCampaign $campaign = null,
        ?int $campaignId = null,
        ?string $externalId = null,
        ?string $state = null,
        ?string $sendToken = null,
    ): array {
        return [
            'campaignId' => $campaignId ?? $campaign?->getId() ?? $message->getCampaignId(),
            'sendToken' => $sendToken ?? $message->getSendToken(),
            'externalId' => $externalId ?? $campaign?->getExternalId(),
            'messengerUniqueId' => $message->getMessengerUniqueId(),
            'state' => $state ?? $campaign?->getBrevoSendState() ?? 'unknown',
            'hostname' => gethostname() ?: null,
        ];
    }
}
