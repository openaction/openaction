<?php

namespace App\Tests\Community\Command;

use App\Bridge\Brevo\BrevoInterface;
use App\Bridge\Brevo\MockBrevo;
use App\Command\Community\SyncBrevoCampaignsReportsCommand;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use App\Repository\Community\EmailingCampaignRepository;
use App\Repository\OrganizationRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SyncBrevoCampaignsReportsCommandTest extends KernelTestCase
{
    public function testSyncsGlobalStatsFromCampaignLevelEndpointOnly(): void
    {
        self::bootKernel();

        /** @var EmailingCampaignRepository $campaignRepository */
        $campaignRepository = static::getContainer()->get(EmailingCampaignRepository::class);
        /** @var OrganizationRepository $organizationRepository */
        $organizationRepository = static::getContainer()->get(OrganizationRepository::class);
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

        /** @var BrevoInterface $brevo */
        $brevo = static::getContainer()->get(BrevoInterface::class);
        $this->assertInstanceOf(MockBrevo::class, $brevo);
        $brevo->campaignsStats = [
            '10' => ['delivered' => 15, 'uniqueViews' => 6, 'uniqueClicks' => 2],
            '20' => ['delivered' => 5, 'uniqueViews' => 1, 'uniqueClicks' => 1],
        ];

        $tester = new CommandTester(static::getContainer()->get(SyncBrevoCampaignsReportsCommand::class));
        $tester->execute(['sent-after' => '-1 day']);

        $em->clear();
        $campaign = $campaignRepository->findOneByUuid('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);
        $this->assertSame(20, $campaign->getGlobalStatsSent());
        $this->assertSame(7, $campaign->getGlobalStatsOpened());
        $this->assertSame(3, $campaign->getGlobalStatsClicked());

        $this->assertCount(1, $brevo->campaignStatsCalls);
        $this->assertSame('brevo_api_key', $brevo->campaignStatsCalls[0]['apiKey']);
        $this->assertInstanceOf(\DateTimeInterface::class, $brevo->campaignStatsCalls[0]['startDate']);
        $this->assertInstanceOf(\DateTimeInterface::class, $brevo->campaignStatsCalls[0]['endDate']);
        $this->assertLessThan(
            $brevo->campaignStatsCalls[0]['endDate']->getTimestamp(),
            $brevo->campaignStatsCalls[0]['startDate']->getTimestamp(),
        );
        $this->assertSame(0, $brevo->campaignReportCalls);

        $campaign->markSentExternally('30');
        $em->flush();

        $brevo->campaignsStats['30'] = ['sent' => 12, 'uniqueOpens' => 4, 'clickers' => 0];

        $tester->execute(['sent-after' => '-1 day']);

        $em->clear();
        $campaign = $campaignRepository->findOneByUuid('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);
        $this->assertSame(12, $campaign->getGlobalStatsSent());
        $this->assertSame(4, $campaign->getGlobalStatsOpened());
        $this->assertSame(0, $campaign->getGlobalStatsClicked());
        $this->assertSame(0, $brevo->campaignReportCalls);
    }
}
