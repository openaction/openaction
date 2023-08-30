<?php

namespace App\Tests\Controller\Bridge;

use App\Community\Webhook\TwilioWebhookMessage;
use App\Tests\WebTestCase;

class TwilioControllerTest extends WebTestCase
{
    public function testWebhook()
    {
        $client = static::createClient();

        $payload = [
            'SmsSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'SmsStatus' => 'sent',
            'MessageStatus' => 'sent',
            'To' => '+33666666666',
            'MessageSid' => 'SMeb0fb1a522e549049fa3db1d5f0e067f',
            'AccountSid' => 'account',
            'From' => 'from',
            'ApiVersion' => '2010-04-01',
        ];

        $client->request('POST', '/webhook/twilio/1', $payload);
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        // Should have published sync message
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(1, $messages = $transport->get());

        /* @var TwilioWebhookMessage $message */
        $this->assertInstanceOf(TwilioWebhookMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($payload, $message->getPayload());
        $this->assertSame('1', $message->getMessageId());
    }
}
