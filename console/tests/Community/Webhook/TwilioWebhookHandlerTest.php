<?php

namespace App\Tests\Community\Webhook;

use App\Community\Webhook\TwilioWebhookHandler;
use App\Community\Webhook\TwilioWebhookMessage;
use App\Entity\Community\TextingCampaignMessage;
use App\Repository\Community\TextingCampaignMessageRepository;
use App\Tests\KernelTestCase;

class TwilioWebhookHandlerTest extends KernelTestCase
{
    public function testSent()
    {
        self::bootKernel();

        $message = $this->findMessage(['sent' => false]);
        $this->executeHandler($message->getId(), [
            'SmsSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'SmsStatus' => 'sent',
            'MessageStatus' => 'sent',
            'To' => '+33666666666',
            'MessageSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'AccountSid' => 'account',
            'From' => 'from',
            'ApiVersion' => '2010-04-01',
        ]);

        $this->assertTrue($this->refreshMessage($message)->isSent());
    }

    public function testDelivered()
    {
        self::bootKernel();

        $message = $this->findMessage(['sent' => false]);
        $this->executeHandler($message->getId(), [
            'SmsSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'SmsStatus' => 'delivered',
            'MessageStatus' => 'delivered',
            'To' => '+33666666666',
            'MessageSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'AccountSid' => 'account',
            'From' => 'from',
            'ApiVersion' => '2010-04-01',
        ]);

        $this->assertTrue($this->refreshMessage($message)->isDelivered());
    }

    public function testUndelivered()
    {
        self::bootKernel();

        $message = $this->findMessage(['sent' => false]);
        $this->executeHandler($message->getId(), [
            'SmsSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'SmsStatus' => 'undelivered',
            'MessageStatus' => 'undelivered',
            'To' => '+33666666666',
            'MessageSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'AccountSid' => 'account',
            'From' => 'from',
            'ApiVersion' => '2010-04-01',
        ]);

        $this->assertTrue($this->refreshMessage($message)->isBounced());
        $this->assertTrue($this->refreshMessage($message)->getContact()->hasSettingsReceiveSms());
    }

    public function testFailed()
    {
        self::bootKernel();

        $message = $this->findMessage(['sent' => false]);
        $this->executeHandler($message->getId(), [
            'SmsSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'SmsStatus' => 'failed',
            'MessageStatus' => 'failed',
            'To' => '+33666666666',
            'MessageSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'AccountSid' => 'account',
            'From' => 'from',
            'ApiVersion' => '2010-04-01',
        ]);

        $this->assertTrue($this->refreshMessage($message)->isBounced());
        $this->assertFalse($this->refreshMessage($message)->getContact()->hasSettingsReceiveSms());
    }

    private function findMessage(array $criteria): ?TextingCampaignMessage
    {
        return clone static::getContainer()->get(TextingCampaignMessageRepository::class)->findOneBy($criteria);
    }

    private function refreshMessage(TextingCampaignMessage $message): ?TextingCampaignMessage
    {
        return clone static::getContainer()->get(TextingCampaignMessageRepository::class)->find($message->getId());
    }

    private function executeHandler(string $messageId, array $payload)
    {
        $handler = static::getContainer()->get(TwilioWebhookHandler::class);
        $handler(new TwilioWebhookMessage($messageId, $payload, '', ''));
    }
}
