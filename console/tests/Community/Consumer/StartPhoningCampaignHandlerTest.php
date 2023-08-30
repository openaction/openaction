<?php

namespace App\Tests\Community\Consumer;

use App\Community\Consumer\StartPhoningCampaignHandler;
use App\Community\Consumer\StartPhoningCampaignMessage;
use App\Entity\Community\PhoningCampaign;
use App\Entity\Community\PhoningCampaignTarget;
use App\Repository\Community\PhoningCampaignRepository;
use App\Repository\Community\PhoningCampaignTargetRepository;
use App\Tests\KernelTestCase;

class StartPhoningCampaignHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(StartPhoningCampaignHandler::class);
        $handler(new StartPhoningCampaignMessage(0));

        // Shouldn't have done anything
        $this->assertCount(0, static::getContainer()->get('messenger.transport.async_priority_low')->get());
    }

    public function testConsumeAlreadyStarted()
    {
        self::bootKernel();

        /** @var PhoningCampaign $campaign */
        $campaign = static::getContainer()->get(PhoningCampaignRepository::class)->findOneByUuid('186314e6-7097-4ad6-9ba1-82030892fcf0');
        $this->assertInstanceOf(PhoningCampaign::class, $campaign);

        $previousMessages = static::getContainer()->get(PhoningCampaignTargetRepository::class)->count(['campaign' => $campaign]);

        $handler = static::getContainer()->get(StartPhoningCampaignHandler::class);
        $handler(new StartPhoningCampaignMessage($campaign->getId()));

        // Shouldn't have done anything
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $messages = $transport->get();
        $this->assertCount(0, $messages);

        // No new message should have been added
        $this->assertSame($previousMessages, static::getContainer()->get(PhoningCampaignTargetRepository::class)->count(['campaign' => $campaign]));
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var PhoningCampaign $campaign */
        $campaign = static::getContainer()->get(PhoningCampaignRepository::class)->findOneByUuid('e5a632df-4960-4d56-bc94-944e0879268e');
        $this->assertInstanceOf(PhoningCampaign::class, $campaign);

        // No message initially
        $this->assertSame(0, static::getContainer()->get(PhoningCampaignTargetRepository::class)->count(['campaign' => $campaign]));

        $handler = static::getContainer()->get(StartPhoningCampaignHandler::class);
        $handler(new StartPhoningCampaignMessage($campaign->getId()));

        // Should have created messages only for subscribed contacts in the project area with a phone number
        /** @var PhoningCampaignTarget[] $targets */
        $targets = static::getContainer()->get(PhoningCampaignTargetRepository::class)->findBy(['campaign' => $campaign]);
        $this->assertCount(2, $targets);

        $recipients = [];
        foreach ($targets as $message) {
            $recipients[] = $message->getContact()->getContactPhone();
        }

        sort($recipients);
        $this->assertSame(['+33 7 57 59 15 57', '+55 61 99881-2130'], $recipients);
    }
}
