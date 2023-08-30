<?php

namespace App\Tests\Controller\Bridge;

use App\Community\Webhook\SendgridWebhookMessage;
use App\Tests\WebTestCase;
use App\Util\Json;

class SendgridControllerTest extends WebTestCase
{
    public function testWebhook()
    {
        $client = static::createClient();

        $payload = Json::encode([['event' => 'delivered', 'message-uuid' => 1]]);
        $client->request('POST', '/webhook/sendgrid', [], [], ['HTTP_ACCEPT' => 'application/ld+json'], $payload);

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        // Should have published sync message
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(1, $messages = $transport->get());

        /* @var SendgridWebhookMessage $message */
        $this->assertInstanceOf(SendgridWebhookMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($payload, $message->getContent());
    }
}
