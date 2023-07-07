<?php

namespace App\Tests\Community\Consumer;

use App\Community\Consumer\CreateTextingCampaignBatchesMessage;
use App\Community\Consumer\SendTextingCampaignHandler;
use App\Community\Consumer\SendTextingCampaignMessage;
use App\Entity\Community\TextingCampaign;
use App\Entity\Community\TextingCampaignMessage;
use App\Repository\Community\TextingCampaignMessageRepository;
use App\Repository\Community\TextingCampaignRepository;
use App\Tests\KernelTestCase;

class SendTextingCampaignHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(SendTextingCampaignHandler::class);
        $handler(new SendTextingCampaignMessage(0));

        // Shouldn't have done anything
        $this->assertCount(0, static::getContainer()->get('messenger.transport.async_priority_low')->get());
    }

    public function testConsumeAlreadySent()
    {
        self::bootKernel();

        /** @var TextingCampaign $campaign */
        $campaign = static::getContainer()->get(TextingCampaignRepository::class)->findOneByUuid('c5a63aa8-e3b0-4ee8-a61b-087e655f77e7');
        $this->assertInstanceOf(TextingCampaign::class, $campaign);

        $previousMessages = static::getContainer()->get(TextingCampaignMessageRepository::class)->count(['campaign' => $campaign]);

        $handler = static::getContainer()->get(SendTextingCampaignHandler::class);
        $handler(new SendTextingCampaignMessage($campaign->getId()));

        // Shouldn't have done anything
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $messages = $transport->get();
        $this->assertCount(0, $messages);

        // No new message should have been added
        $this->assertSame($previousMessages, static::getContainer()->get(TextingCampaignMessageRepository::class)->count(['campaign' => $campaign]));
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var TextingCampaign $campaign */
        $campaign = static::getContainer()->get(TextingCampaignRepository::class)->findOneByUuid('e4a3799d-b217-4389-b89e-beef08bdbbd3');
        $this->assertInstanceOf(TextingCampaign::class, $campaign);

        // No message initially
        $this->assertSame(0, static::getContainer()->get(TextingCampaignMessageRepository::class)->count(['campaign' => $campaign]));

        $handler = static::getContainer()->get(SendTextingCampaignHandler::class);
        $handler(new SendTextingCampaignMessage($campaign->getId()));

        // Should have created messages only for subscribed contacts in the project area with a phone number
        /** @var TextingCampaignMessage[] $messages */
        $messages = static::getContainer()->get(TextingCampaignMessageRepository::class)->findBy(['campaign' => $campaign]);
        $this->assertCount(3, $messages);

        $recipients = [];
        foreach ($messages as $message) {
            $recipients[] = $message->getContact()->getContactPhone();
        }

        sort($recipients);

        $this->assertSame(['+33 7 57 59 15 57', '+33 7 57 59 15 59', '+55 61 99881-2130'], $recipients);

        // Should have published batches
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(CreateTextingCampaignBatchesMessage::class, $messages[0]->getMessage());
        $this->assertSame($campaign->getId(), $messages[0]->getMessage()->getCampaignId());
    }
}
