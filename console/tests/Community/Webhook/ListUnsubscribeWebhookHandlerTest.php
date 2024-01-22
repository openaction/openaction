<?php

namespace App\Tests\Community\Webhook;

use App\Community\Webhook\ListUnsubscribeWebhookHandler;
use App\Community\Webhook\ListUnsubscribeWebhookMessage;
use App\Repository\Community\ContactRepository;
use App\Tests\KernelTestCase;

class ListUnsubscribeWebhookHandlerTest extends KernelTestCase
{
    public function testUnsubscribe()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ListUnsubscribeWebhookHandler::class);
        $handler(new ListUnsubscribeWebhookMessage('104SKb9m0xnYyt8OiWn3ks'));

        /** @var ContactRepository $repository */
        $repository = static::getContainer()->get(ContactRepository::class);
        $contact = $repository->findOneBy(['uuid' => '20e51b91-bdec-495d-854d-85d6e74fc75e']);

        $this->assertFalse($contact->hasSettingsReceiveNewsletters());
    }
}
