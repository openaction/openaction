<?php

namespace App\Tests\Community\Consumer;

use App\Community\Consumer\CreateEmailingCampaignBatchesMessage;
use App\Community\Consumer\SendEmailingCampaignHandler;
use App\Community\Consumer\SendEmailingCampaignMessage;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Community\EmailingCampaignMessage;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use App\Tests\KernelTestCase;

class SendEmailingCampaignHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(SendEmailingCampaignHandler::class);
        $handler(new SendEmailingCampaignMessage(0));

        // Shouldn't have done anything
        $this->assertCount(0, static::getContainer()->get('messenger.transport.async_priority_low')->get());
    }

    public function testConsumeAlreadySent()
    {
        self::bootKernel();

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneByUuid('95b3f576-c643-45ba-9d5e-c9c44f65fab8');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);

        $previousMessages = static::getContainer()->get(EmailingCampaignMessageRepository::class)->count(['campaign' => $campaign]);

        $handler = static::getContainer()->get(SendEmailingCampaignHandler::class);
        $handler(new SendEmailingCampaignMessage($campaign->getId()));

        // Shouldn't have done anything
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $messages = $transport->get();
        $this->assertCount(0, $messages);

        // No new message should have been added
        $this->assertSame($previousMessages, static::getContainer()->get(EmailingCampaignMessageRepository::class)->count(['campaign' => $campaign]));
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneByUuid('10808026-bbae-4db5-a8ab-8abecb50102c');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);

        // No message initially
        $this->assertSame(0, static::getContainer()->get(EmailingCampaignMessageRepository::class)->count(['campaign' => $campaign]));

        $handler = static::getContainer()->get(SendEmailingCampaignHandler::class);
        $handler(new SendEmailingCampaignMessage($campaign->getId()));

        // Should have created messages only for subscribed contacts in the project area
        /** @var EmailingCampaignMessage[] $messages */
        $messages = static::getContainer()->get(EmailingCampaignMessageRepository::class)->findBy(['campaign' => $campaign]);
        $this->assertCount(3, $messages);

        $recipients = [];
        foreach ($messages as $message) {
            $recipients[] = $message->getContact()->getEmail();
        }

        sort($recipients);

        $this->assertSame(
            ['a.compagnon@protonmail.com', 'apolline.mousseau@rpr.fr', 'brunella.courtemanche2@orange.fr'],
            $recipients
        );

        // Should have published batches
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(CreateEmailingCampaignBatchesMessage::class, $messages[0]->getMessage());
        $this->assertSame($campaign->getId(), $messages[0]->getMessage()->getCampaignId());
    }
}
