<?php

namespace App\Tests\Community\Command;

use App\Bridge\Brevo\BrevoInterface;
use App\Bridge\Brevo\MockBrevo;
use App\Command\Community\SyncBrevoCampaignsReportsCommand;
use App\Community\ContactViewBuilder;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use App\Repository\OrganizationRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SyncBrevoCampaignsReportsCommandTest extends KernelTestCase
{
    public function testAggregatesReportsAcrossMultipleExternalCampaignIdsAndKeepsSingleIdCompatibility(): void
    {
        self::bootKernel();

        /** @var EmailingCampaignRepository $campaignRepository */
        $campaignRepository = static::getContainer()->get(EmailingCampaignRepository::class);
        /** @var OrganizationRepository $organizationRepository */
        $organizationRepository = static::getContainer()->get(OrganizationRepository::class);
        /** @var EmailingCampaignMessageRepository $messageRepository */
        $messageRepository = static::getContainer()->get(EmailingCampaignMessageRepository::class);
        /** @var ContactViewBuilder $contactViewBuilder */
        $contactViewBuilder = static::getContainer()->get(ContactViewBuilder::class);
        $em = static::getContainer()->get('doctrine')->getManager();

        /** @var EmailingCampaign $campaign */
        $campaign = $campaignRepository->findOneByUuid('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);

        /** @var Organization $organization */
        $organization = $organizationRepository->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $organization->setEmailProvider('brevo');
        $organization->setBrevoApiKey('brevo_api_key');
        $campaign->markSentExternally('10,20');
        $em->flush();

        $messageRepository->createCampaignMessages(
            $campaign,
            $contactViewBuilder->forEmailingCampaign($campaign)->createQueryBuilder(),
            sent: true,
        );

        $messages = $messageRepository->findBy(['campaign' => $campaign], ['id' => 'ASC']);
        $this->assertCount(3, $messages);

        $firstEmail = $messages[0]->getContact()->getEmail();
        $secondEmail = $messages[1]->getContact()->getEmail();
        $thirdEmail = $messages[2]->getContact()->getEmail();
        $this->assertNotNull($firstEmail);
        $this->assertNotNull($secondEmail);
        $this->assertNotNull($thirdEmail);

        /** @var BrevoInterface $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);
        $brevo->reports = [
            '10' => [
                $firstEmail => ['sent' => true, 'opened' => false, 'clicked' => false, 'bounced' => false],
            ],
            '20' => [
                $firstEmail => ['sent' => true, 'opened' => true, 'clicked' => true, 'bounced' => false],
                $secondEmail => ['sent' => false, 'opened' => false, 'clicked' => false, 'bounced' => true],
            ],
        ];

        $tester = new CommandTester(static::getContainer()->get(SyncBrevoCampaignsReportsCommand::class));
        $tester->execute(['sent-after' => '-1 day']);

        $em->clear();
        $campaign = $campaignRepository->findOneByUuid('10808026-bbae-4db5-a8ab-8abecb50102c');
        $messages = $messageRepository->findBy(['campaign' => $campaign], ['id' => 'ASC']);

        $this->assertTrue($messages[0]->isSent());
        $this->assertTrue($messages[0]->isOpened());
        $this->assertTrue($messages[0]->isClicked());
        $this->assertFalse($messages[0]->isBounced());

        $this->assertFalse($messages[1]->isSent());
        $this->assertFalse($messages[1]->isOpened());
        $this->assertFalse($messages[1]->isClicked());
        $this->assertTrue($messages[1]->isBounced());

        $campaign->markSentExternally('30');
        $em->flush();

        $brevo->reports['30'] = [
            $thirdEmail => ['sent' => true, 'opened' => true, 'clicked' => false, 'bounced' => false],
        ];

        $tester->execute(['sent-after' => '-1 day']);

        $em->clear();
        $campaign = $campaignRepository->findOneByUuid('10808026-bbae-4db5-a8ab-8abecb50102c');
        $messages = $messageRepository->findBy(['campaign' => $campaign], ['id' => 'ASC']);

        $this->assertTrue($messages[2]->isSent());
        $this->assertTrue($messages[2]->isOpened());
        $this->assertFalse($messages[2]->isClicked());
        $this->assertFalse($messages[2]->isBounced());
    }
}
