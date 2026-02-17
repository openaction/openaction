<?php

namespace App\Community\Consumer;

use App\Bridge\Brevo\BrevoInterface;
use App\Community\ContactViewBuilder;
use App\Community\SendgridMailFactory;
use App\Entity\Organization;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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

        if (null === $brevoCampaignId) {
            $brevoCampaignId = $this->normalizeBrevoCampaignId($this->brevo->createEmailCampaign(
                campaign: $campaign,
                htmlContent: $htmlContent,
                listId: $brevoListId,
            ));

            if (null === $brevoCampaignId) {
                throw new \RuntimeException('Brevo error: campaign could not be created.');
            }

            $campaign->setExternalId($brevoCampaignId);
            $this->em->persist($campaign);
            $this->em->flush();
        }

        if (!$this->brevo->isEmailCampaignSent($campaign, $brevoCampaignId)) {
            $this->brevo->sendEmailCampaignNow($campaign, $brevoCampaignId);
        }

        $this->messageRepository->createCampaignMessages(
            $campaign,
            $contactsQueryBuilder,
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
}
