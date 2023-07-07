<?php

namespace App\Tests\Community\Consumer;

use App\Bridge\Twilio\Consumer\TwilioMessage;
use App\Bridge\Twilio\Model\Personalization;
use App\Community\Consumer\CreateTextingCampaignBatchesHandler;
use App\Community\Consumer\CreateTextingCampaignBatchesMessage;
use App\Entity\Community\TextingCampaign;
use App\Repository\Community\TextingCampaignRepository;
use App\Tests\KernelTestCase;

class CreateTextingCampaignBatchesHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = self::getContainer()->get(CreateTextingCampaignBatchesHandler::class);
        $handler(new CreateTextingCampaignBatchesMessage(0));

        // Shouldn't have done anything
        $transport = self::getContainer()->get('messenger.transport.async_texting');
        $messages = $transport->get();
        $this->assertCount(0, $messages);
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var TextingCampaign $campaign */
        $campaign = self::getContainer()->get(TextingCampaignRepository::class)->findOneByUuid('c4d39567-f3ef-4f46-ac2f-d7573a5456d9');
        $this->assertInstanceOf(TextingCampaign::class, $campaign);

        $handler = self::getContainer()->get(CreateTextingCampaignBatchesHandler::class);
        $handler(new CreateTextingCampaignBatchesMessage($campaign->getId()));

        // Should have published batches
        $transport = self::getContainer()->get('messenger.transport.async_texting');
        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var TwilioMessage $textMessage */
        $textMessage = $messages[0]->getMessage();
        $this->assertInstanceOf(TwilioMessage::class, $textMessage);
        $this->assertSame('CPGT', $textMessage->getFrom());
        $this->assertSame('Go vote for Auralp on 20th and 27th of June!', $textMessage->getBody());
        $this->assertSame(
            ['+33757591557'],
            array_map(static fn (Personalization $p) => $p->getTo(), $textMessage->getPersonalizations())
        );
    }
}
