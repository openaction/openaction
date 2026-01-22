<?php

namespace App\Tests\Community\Consumer;

use App\Community\Consumer\CreateEmailingCampaignBatchesHandler;
use App\Community\Consumer\CreateEmailingCampaignBatchesMessage;
use App\Entity\Community\EmailingCampaign;
use App\Repository\Community\EmailBatchRepository;
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

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneByUuid('95b3f576-c643-45ba-9d5e-c9c44f65fab8');
        $this->assertInstanceOf(EmailingCampaign::class, $campaign);

        $handler = static::getContainer()->get(CreateEmailingCampaignBatchesHandler::class);
        $before = new \DateTimeImmutable();
        $handler(new CreateEmailingCampaignBatchesMessage($campaign->getId()));
        $after = new \DateTimeImmutable();

        // Should have scheduled batches without dispatching them immediately
        $transport = static::getContainer()->get('messenger.transport.async_emailing');
        $messages = $transport->get();
        $this->assertCount(0, $messages);

        /** @var EmailBatchRepository $repository */
        $repository = static::getContainer()->get(EmailBatchRepository::class);
        $batches = $repository->findBy(['source' => 'campaign:'.$campaign->getId()]);
        $this->assertCount(1, $batches);

        $batch = $batches[0];
        $this->assertNotNull($batch->getScheduledAt());
        $this->assertNull($batch->getQueuedAt());
        $this->assertGreaterThanOrEqual($before->getTimestamp(), $batch->getScheduledAt()->getTimestamp());
        $this->assertLessThanOrEqual($after->getTimestamp(), $batch->getScheduledAt()->getTimestamp());
    }
}
