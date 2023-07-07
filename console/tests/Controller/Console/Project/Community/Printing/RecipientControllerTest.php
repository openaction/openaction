<?php

namespace App\Tests\Controller\Console\Project\Community\Printing;

use App\Entity\Community\PrintingOrder;
use App\Repository\Community\PrintingOrderRepository;
use App\Tests\WebTestCase;

class RecipientControllerTest extends WebTestCase
{
    private const DRAFT_UUID = '7e3617e3-b147-4f53-864c-1550d65ddbc4';

    public function testDeliveryUnaddressedIndividual()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::DRAFT_UUID.'/recipient');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="printing_order_recipient"]')->form(), [
            'printing_order_recipient[candidate]' => 'Jeanne Martin',
            'printing_order_recipient[circonscription]' => '01-3',
            'printing_order_recipient[firstName]' => 'FirstName',
            'printing_order_recipient[lastName]' => 'LastName',
            'printing_order_recipient[email]' => 'email@gmail.com',
            'printing_order_recipient[phone]' => '0707070707',
        ]);
        $this->assertResponseRedirects();

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_UUID]);
        $this->assertSame('Jeanne Martin', $order->getRecipientCandidate());
        $this->assertSame('01', $order->getRecipientDepartment());
        $this->assertSame('3', $order->getRecipientCirconscription());
        $this->assertSame('FirstName', $order->getRecipientFirstName());
        $this->assertSame('LastName', $order->getRecipientLastName());
        $this->assertSame('email@gmail.com', $order->getRecipientEmail());
        $this->assertSame('0707070707', $order->getRecipientPhone());
    }
}
