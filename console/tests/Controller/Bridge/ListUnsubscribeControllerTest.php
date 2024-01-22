<?php

namespace App\Tests\Controller\Bridge;

use App\Community\Webhook\ListUnsubscribeWebhookMessage;
use App\Tests\WebTestCase;

class ListUnsubscribeControllerTest extends WebTestCase
{
    public function testWebhook()
    {
        $client = static::createClient();
        $client->request('POST', '/webhook/list-unsubscribe/104SKb9m0xnYyt8OiWn3ks');
        $this->assertResponseRedirects('https://citipo.com/newsletter?unsubscribe=1');

        // Should have published sync message
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(1, $messages = $transport->get());

        /* @var ListUnsubscribeWebhookMessage $message */
        $this->assertInstanceOf(ListUnsubscribeWebhookMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame('104SKb9m0xnYyt8OiWn3ks', $message->getContactUuid());
    }
}
