<?php

namespace App\Tests\Community\Webhook;

use App\Community\Webhook\SendgridWebhookHandler;
use App\Community\Webhook\SendgridWebhookMessage;
use App\Entity\Community\EmailingCampaignMessage;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Tests\KernelTestCase;
use App\Util\Json;

class SendgridWebhookHandlerTest extends KernelTestCase
{
    public function testDelivered()
    {
        self::bootKernel();

        $message = $this->findMessage(['sent' => false]);
        $this->executeHandler([['event' => 'delivered', 'message-uuid' => $message->getId()]]);

        $this->assertTrue($this->refreshMessage($message)->isSent());
    }

    public function testOpen()
    {
        self::bootKernel();

        $message = $this->findMessage(['opened' => false]);
        $this->executeHandler([['event' => 'open', 'message-uuid' => $message->getId()]]);

        $this->assertTrue($this->refreshMessage($message)->isSent());
        $this->assertTrue($this->refreshMessage($message)->isOpened());
    }

    public function testClick()
    {
        self::bootKernel();

        $message = $this->findMessage(['clicked' => false]);
        $this->executeHandler([['event' => 'click', 'message-uuid' => $message->getId()]]);

        $this->assertTrue($this->refreshMessage($message)->isSent());
        $this->assertTrue($this->refreshMessage($message)->isOpened());
        $this->assertTrue($this->refreshMessage($message)->isClicked());
    }

    public function testUnsubscribe()
    {
        self::bootKernel();

        $message = $this->findMessage([]);
        $this->executeHandler([['event' => 'unsubscribe', 'message-uuid' => $message->getId()]]);

        $this->assertNotNull($this->refreshMessage($message));
    }

    public function testSpamreport()
    {
        self::bootKernel();

        $message = $this->findMessage([]);
        $this->executeHandler([['event' => 'spamreport', 'message-uuid' => $message->getId()]]);

        $this->assertNotNull($this->refreshMessage($message));
    }

    public function testBounce()
    {
        self::bootKernel();

        $message = $this->findMessage([]);
        $this->executeHandler([['event' => 'bounce', 'message-uuid' => $message->getId()]]);

        $this->assertTrue($this->refreshMessage($message)->isBounced());
    }

    public function testDropped()
    {
        self::bootKernel();

        $message = $this->findMessage([]);
        $this->executeHandler([['event' => 'dropped', 'message-uuid' => $message->getId()]]);

        $this->assertTrue($this->refreshMessage($message)->isBounced());
    }

    private function findMessage(array $criteria): ?EmailingCampaignMessage
    {
        return clone static::getContainer()->get(EmailingCampaignMessageRepository::class)->findOneBy($criteria);
    }

    private function refreshMessage(EmailingCampaignMessage $message): ?EmailingCampaignMessage
    {
        return clone static::getContainer()->get(EmailingCampaignMessageRepository::class)->find($message->getId());
    }

    private function executeHandler(array $payload)
    {
        $handler = static::getContainer()->get(SendgridWebhookHandler::class);
        $handler(new SendgridWebhookMessage(Json::encode($payload), '', ''));
    }
}
