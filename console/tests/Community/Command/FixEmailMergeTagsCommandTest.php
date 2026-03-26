<?php

namespace App\Tests\Community\Command;

use App\Command\Community\FixEmailMergeTagsCommand;
use App\Entity\Community\EmailAutomation;
use App\Entity\Community\EmailingCampaign;
use App\Repository\Community\EmailAutomationRepository;
use App\Repository\Community\EmailingCampaignRepository;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Tester\CommandTester;

class FixEmailMergeTagsCommandTest extends KernelTestCase
{
    public function testDryRunAndRealModeReplaceLegacyMergeTags(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        /** @var EmailAutomationRepository $automationRepository */
        $automationRepository = static::getContainer()->get(EmailAutomationRepository::class);
        /** @var EmailingCampaignRepository $campaignRepository */
        $campaignRepository = static::getContainer()->get(EmailingCampaignRepository::class);

        /** @var EmailAutomation $automation */
        $automation = $automationRepository->findOneByUuid('f2185892-9b3a-4eab-8a81-3520cded8571');
        $this->assertInstanceOf(EmailAutomation::class, $automation);
        $automation->applyUnlayerUpdate(
            ['text' => '-contact-formaltitle- / -contact-streetline1-'],
            'Legacy automation content -contact-formaltitle- / -contact-jobtitle- / -contact-streetline2-',
        );

        /** @var EmailingCampaign $campaign */
        $campaign = $campaignRepository->findOneByUuid('95b3f576-c643-45ba-9d5e-c9c44f65fab8');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);
        $campaign->applyUnlayerUpdate(
            ['text' => '-contact-jobtitle- / -contact-streetline2-'],
            'Legacy campaign content -contact-formaltitle- / -contact-streetline1-',
        );

        $em->flush();

        $tester = new CommandTester(static::getContainer()->get(FixEmailMergeTagsCommand::class));
        $tester->execute(['--dry-run' => true]);
        $display = $tester->getDisplay();

        $this->assertMatchesRegularExpression('/community_email_automations\.content: [1-9][0-9]* row\(s\)/', $display);
        $this->assertMatchesRegularExpression('/community_email_automations\.unlayer_design: [1-9][0-9]* row\(s\)/', $display);
        $this->assertMatchesRegularExpression('/community_emailing_campaigns\.content: [1-9][0-9]* row\(s\)/', $display);
        $this->assertMatchesRegularExpression('/community_emailing_campaigns\.unlayer_design: [1-9][0-9]* row\(s\)/', $display);

        $tester->execute([]);

        $em->clear();

        /** @var EmailAutomation $automation */
        $automation = $automationRepository->findOneByUuid('f2185892-9b3a-4eab-8a81-3520cded8571');
        $this->assertInstanceOf(EmailAutomation::class, $automation);
        $this->assertStringContainsString('-contact-formal-title-', (string) $automation->getContent());
        $this->assertStringContainsString('-contact-job-title-', (string) $automation->getContent());
        $this->assertStringContainsString('-contact-streetline-2-', (string) $automation->getContent());
        $this->assertStringNotContainsString('-contact-formaltitle-', (string) $automation->getContent());
        $this->assertStringNotContainsString('-contact-jobtitle-', (string) $automation->getContent());
        $this->assertStringNotContainsString('-contact-streetline2-', (string) $automation->getContent());

        $automationDesign = json_encode($automation->getUnlayerDesign(), \JSON_THROW_ON_ERROR);
        $this->assertStringContainsString('-contact-formal-title-', $automationDesign);
        $this->assertStringContainsString('-contact-streetline-1-', $automationDesign);
        $this->assertStringNotContainsString('-contact-formaltitle-', $automationDesign);
        $this->assertStringNotContainsString('-contact-streetline1-', $automationDesign);

        /** @var EmailingCampaign $campaign */
        $campaign = $campaignRepository->findOneByUuid('95b3f576-c643-45ba-9d5e-c9c44f65fab8');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);
        $this->assertStringContainsString('-contact-formal-title-', (string) $campaign->getContent());
        $this->assertStringContainsString('-contact-streetline-1-', (string) $campaign->getContent());
        $this->assertStringNotContainsString('-contact-formaltitle-', (string) $campaign->getContent());
        $this->assertStringNotContainsString('-contact-streetline1-', (string) $campaign->getContent());

        $campaignDesign = json_encode($campaign->getUnlayerDesign(), \JSON_THROW_ON_ERROR);
        $this->assertStringContainsString('-contact-job-title-', $campaignDesign);
        $this->assertStringContainsString('-contact-streetline-2-', $campaignDesign);
        $this->assertStringNotContainsString('-contact-jobtitle-', $campaignDesign);
        $this->assertStringNotContainsString('-contact-streetline2-', $campaignDesign);
    }
}
