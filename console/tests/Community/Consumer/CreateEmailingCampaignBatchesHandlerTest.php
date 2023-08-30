<?php

namespace App\Tests\Community\Consumer;

use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Community\Consumer\CreateEmailingCampaignBatchesHandler;
use App\Community\Consumer\CreateEmailingCampaignBatchesMessage;
use App\Entity\Community\EmailingCampaign;
use App\Repository\Community\EmailingCampaignRepository;
use App\Tests\KernelTestCase;

class CreateEmailingCampaignBatchesHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(CreateEmailingCampaignBatchesHandler::class);
        $handler(new CreateEmailingCampaignBatchesMessage(0));

        // Shouldn't have done anything
        $transport = static::getContainer()->get('messenger.transport.async_emailing');
        $messages = $transport->get();
        $this->assertCount(0, $messages);
    }

    public function testConsume()
    {
        self::bootKernel();

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneByUuid('95b3f576-c643-45ba-9d5e-c9c44f65fab8');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);

        $handler = static::getContainer()->get(CreateEmailingCampaignBatchesHandler::class);
        $handler(new CreateEmailingCampaignBatchesMessage($campaign->getId()));

        // Should have published batches
        $transport = static::getContainer()->get('messenger.transport.async_emailing');
        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(SendgridMessage::class, $messages[0]->getMessage());
    }
}
