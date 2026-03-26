<?php

namespace App\Tests\Community\Consumer;

use App\Bridge\Brevo\BrevoInterface;
use App\Bridge\Brevo\MockBrevo;
use App\Community\Consumer\SendBrevoEmailingCampaignHandler;
use App\Community\Consumer\SendBrevoEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use App\Repository\OrganizationRepository;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class SendBrevoEmailingCampaignHandlerTest extends KernelTestCase
{
    public function testConsumeValidWithoutThrottling(): void
    {
        self::bootKernel();

        $campaign = $this->findCampaign('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->configureBrevoOrganization(emailThrottlingPerHour: null);

        $this->assertSame(0, static::getContainer()->get(EmailingCampaignMessageRepository::class)->count(['campaign' => $campaign]));

        /** @var MockBrevo $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);

        $sendToken = $this->claimCampaign($campaign);

        $handler = static::getContainer()->get(SendBrevoEmailingCampaignHandler::class);
        $handler(new SendBrevoEmailingCampaignMessage($campaign->getId(), $sendToken, 'msg-valid-no-throttle'));

        $this->assertCount(1, $brevo->campaigns);
        $payload = $brevo->campaigns['1'];
        $this->assertNull($payload['scheduledAt']);
        $this->assertNull($payload['batching']);
        $this->assertSame('1', $campaign->getExternalId());
        $this->assertSame(1, $campaign->getExternalListId());
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_SENT, $campaign->getBrevoSendState());
        $this->assertNotNull($campaign->getBrevoRemoteCreatedAt());
        $this->assertNotNull($campaign->getBrevoRemoteSentAt());
        $this->assertNotNull($campaign->getResolvedAt());
        $this->assertNotNull($campaign->getSentAt());
        $this->assertCount(3, $payload['contacts']);
        $this->assertStringContainsString('Hello world', $payload['html']);
        foreach ($payload['contacts'] as $contact) {
            $this->assertArrayHasKey('email', $contact);
            $this->assertArrayHasKey('phone', $contact);
            $this->assertArrayHasKey('formalTitle', $contact);
            $this->assertArrayHasKey('firstName', $contact);
            $this->assertArrayHasKey('lastName', $contact);
            $this->assertArrayHasKey('fullName', $contact);
            $this->assertArrayHasKey('gender', $contact);
            $this->assertArrayHasKey('nationality', $contact);
            $this->assertArrayHasKey('company', $contact);
            $this->assertArrayHasKey('jobTitle', $contact);
            $this->assertArrayHasKey('addressLine1', $contact);
            $this->assertArrayHasKey('addressLine2', $contact);
            $this->assertArrayHasKey('postalCode', $contact);
            $this->assertArrayHasKey('city', $contact);
            $this->assertArrayHasKey('country', $contact);
        }

        $messages = static::getContainer()->get(EmailingCampaignMessageRepository::class)->findBy(['campaign' => $campaign]);
        $this->assertCount(3, $messages);

        foreach ($messages as $message) {
            $this->assertTrue($message->isSent());
        }
    }

    public function testConsumeValidWithThrottling(): void
    {
        self::bootKernel();

        $campaign = $this->findCampaign('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->configureBrevoOrganization(emailThrottlingPerHour: 2);

        /** @var MockBrevo $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);

        $sendToken = $this->claimCampaign($campaign);

        $handler = static::getContainer()->get(SendBrevoEmailingCampaignHandler::class);
        $handler(new SendBrevoEmailingCampaignMessage($campaign->getId(), $sendToken, 'msg-valid-throttle'));

        $this->assertCount(1, $brevo->campaigns);
        $payload = $brevo->campaigns['1'];
        $this->assertSame('1', $campaign->getExternalId());
        $this->assertSame(1, $campaign->getExternalListId());
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_SENT, $campaign->getBrevoSendState());
        $this->assertCount(3, $payload['contacts']);
        $this->assertNotNull($payload['scheduledAt']);
        $this->assertSame([
            'batchSize' => 2,
            'batchesCount' => 2,
            'intervalMinutes' => 30,
        ], $payload['batching']);

        $messages = static::getContainer()->get(EmailingCampaignMessageRepository::class)->findBy(['campaign' => $campaign]);
        $this->assertCount(3, $messages);

        foreach ($messages as $message) {
            $this->assertTrue($message->isSent());
        }
    }

    public function testConsumeRetryReusesPersistedIdsAndSendsPendingCampaign(): void
    {
        self::bootKernel();

        $campaign = $this->findCampaign('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->configureBrevoOrganization(emailThrottlingPerHour: null);

        /** @var MockBrevo $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);

        $sendToken = $this->claimCampaign($campaign);

        $listId = $brevo->createCampaignList($campaign);
        $campaignId = $brevo->createEmailCampaign($campaign, '<p>Existing campaign</p>', $listId);

        $campaign->setExternalListId($listId);
        $campaign->setExternalId($campaignId);
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $createListCallsBefore = $brevo->createCampaignListCalls;
        $createCampaignCallsBefore = $brevo->createEmailCampaignCalls;
        $statusCallsBefore = $brevo->isEmailCampaignSentCalls;
        $sendCallsBefore = $brevo->sendEmailCampaignNowCalls;

        $handler = static::getContainer()->get(SendBrevoEmailingCampaignHandler::class);
        $handler(new SendBrevoEmailingCampaignMessage($campaign->getId(), $sendToken, 'msg-retry-pending'));

        $this->assertSame($createListCallsBefore, $brevo->createCampaignListCalls);
        $this->assertSame($createCampaignCallsBefore, $brevo->createEmailCampaignCalls);
        $this->assertSame($statusCallsBefore + 1, $brevo->isEmailCampaignSentCalls);
        $this->assertSame($sendCallsBefore + 1, $brevo->sendEmailCampaignNowCalls);
        $this->assertSame((string) $campaignId, $campaign->getExternalId());
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_SENT, $campaign->getBrevoSendState());
        $this->assertNotNull($campaign->getBrevoRemoteCreatedAt());
        $this->assertNotNull($campaign->getBrevoRemoteSentAt());

        $messages = static::getContainer()->get(EmailingCampaignMessageRepository::class)->findBy(['campaign' => $campaign]);
        $this->assertCount(3, $messages);
    }

    public function testConsumeRetrySkipsSendWhenCampaignAlreadySentRemotely(): void
    {
        self::bootKernel();

        $campaign = $this->findCampaign('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->configureBrevoOrganization(emailThrottlingPerHour: null);

        /** @var MockBrevo $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);

        $sendToken = $this->claimCampaign($campaign);

        $listId = $brevo->createCampaignList($campaign);
        $campaignId = $brevo->createEmailCampaign($campaign, '<p>Existing campaign</p>', $listId);
        $brevo->sendEmailCampaignNow($campaign, $campaignId);

        $campaign->setExternalListId($listId);
        $campaign->setExternalId($campaignId);
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $createListCallsBefore = $brevo->createCampaignListCalls;
        $createCampaignCallsBefore = $brevo->createEmailCampaignCalls;
        $statusCallsBefore = $brevo->isEmailCampaignSentCalls;
        $sendCallsBefore = $brevo->sendEmailCampaignNowCalls;

        $handler = static::getContainer()->get(SendBrevoEmailingCampaignHandler::class);
        $handler(new SendBrevoEmailingCampaignMessage($campaign->getId(), $sendToken, 'msg-retry-sent'));

        $this->assertSame($createListCallsBefore, $brevo->createCampaignListCalls);
        $this->assertSame($createCampaignCallsBefore, $brevo->createEmailCampaignCalls);
        $this->assertSame($statusCallsBefore + 1, $brevo->isEmailCampaignSentCalls);
        $this->assertSame($sendCallsBefore, $brevo->sendEmailCampaignNowCalls);
        $this->assertSame((string) $campaignId, $campaign->getExternalId());
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_SENT, $campaign->getBrevoSendState());
        $this->assertNotNull($campaign->getBrevoRemoteCreatedAt());
        $this->assertNotNull($campaign->getBrevoRemoteSentAt());

        $messages = static::getContainer()->get(EmailingCampaignMessageRepository::class)->findBy(['campaign' => $campaign]);
        $this->assertCount(3, $messages);
    }

    public function testTokenMismatchMessageIsIgnored(): void
    {
        self::bootKernel();

        $campaign = $this->findCampaign('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->configureBrevoOrganization(emailThrottlingPerHour: null);

        /** @var MockBrevo $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);

        $this->claimCampaign($campaign);

        $handler = static::getContainer()->get(SendBrevoEmailingCampaignHandler::class);
        $handler(new SendBrevoEmailingCampaignMessage($campaign->getId(), 'different-token', 'msg-token-mismatch'));

        $this->assertSame(0, $brevo->createEmailCampaignCalls);
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_QUEUED, $campaign->getBrevoSendState());
    }

    public function testLegacyMessageWithoutTokenClaimsDraftAndSendsCampaign(): void
    {
        self::bootKernel();

        $campaign = $this->findCampaign('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->configureBrevoOrganization(emailThrottlingPerHour: null);
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_DRAFT, $campaign->getBrevoSendState());

        /** @var MockBrevo $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);

        $handler = static::getContainer()->get(SendBrevoEmailingCampaignHandler::class);
        $handler(new SendBrevoEmailingCampaignMessage($campaign->getId(), null, 'msg-legacy-no-token'));

        $this->assertSame(1, $brevo->createEmailCampaignCalls);
        $this->assertSame(1, $brevo->sendEmailCampaignNowCalls);
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_SENT, $campaign->getBrevoSendState());
        $this->assertNotNull($campaign->getSendToken());
    }

    public function testRetryAfterRemoteCreateWithoutLocalCheckpointDoesNotCreateSecondCampaign(): void
    {
        self::bootKernel();

        $campaign = $this->findCampaign('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->configureBrevoOrganization(emailThrottlingPerHour: null);

        /** @var MockBrevo $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);

        $sendToken = $this->claimCampaign($campaign);
        $this->assertNotNull($campaign->getBrevoDedupKey());

        $precreatedListId = $brevo->createCampaignList($campaign);
        $precreatedCampaignId = $brevo->createEmailCampaign($campaign, '<p>Pre-created</p>', $precreatedListId);

        $campaign->setExternalListId($precreatedListId);
        $campaign->setExternalId(null);
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $createCampaignCallsBefore = $brevo->createEmailCampaignCalls;

        $handler = static::getContainer()->get(SendBrevoEmailingCampaignHandler::class);
        $handler(new SendBrevoEmailingCampaignMessage($campaign->getId(), $sendToken, 'msg-retry-dedup'));

        $this->assertSame($createCampaignCallsBefore, $brevo->createEmailCampaignCalls);
        $this->assertSame((string) $precreatedCampaignId, $campaign->getExternalId());
        $this->assertNotNull($campaign->getBrevoRemoteCreatedAt());
        $this->assertNotNull($campaign->getBrevoRemoteSentAt());
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_SENT, $campaign->getBrevoSendState());
    }

    private function findCampaign(string $uuid): EmailingCampaign
    {
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneByUuid($uuid);
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);

        return $campaign;
    }

    private function configureBrevoOrganization(?int $emailThrottlingPerHour): void
    {
        /** @var Organization $organization */
        $organization = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $organization->setEmailProvider('brevo');
        $organization->setBrevoApiKey('brevo_api_key');
        $organization->setBrevoSenderEmail('brevo@example.test');
        $organization->setEmailThrottlingPerHour($emailThrottlingPerHour);

        static::getContainer()->get(EntityManagerInterface::class)->flush();
    }

    private function claimCampaign(EmailingCampaign $campaign): string
    {
        $sendToken = static::getContainer()->get(EmailingCampaignRepository::class)->claimBrevoSend($campaign);
        $this->assertNotNull($sendToken);

        static::getContainer()->get(EntityManagerInterface::class)->refresh($campaign);
        $this->assertSame(EmailingCampaign::BREVO_SEND_STATE_QUEUED, $campaign->getBrevoSendState());

        return $sendToken;
    }
}
