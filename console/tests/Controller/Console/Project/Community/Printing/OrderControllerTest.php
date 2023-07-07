<?php

namespace App\Tests\Controller\Console\Project\Community\Printing;

use App\Bridge\Mollie\MockMollie;
use App\Bridge\Mollie\MollieInterface;
use App\Community\Printing\Consumer\RequestCampaignPreflightMessage;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Community\PrintingOrder;
use App\Entity\Organization;
use App\Repository\Community\PrintingOrderRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Order as MollieOrder;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class OrderControllerTest extends WebTestCase
{
    private const DRAFT_UUID = '7e3617e3-b147-4f53-864c-1550d65ddbc4';
    private const ALREADY_ORDERED_UUID = 'fbf6c9dd-ca6f-43eb-8472-331d34038939';

    public function testOrderAlreadyOrdered()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::ALREADY_ORDERED_UUID.'/order');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testOrderValid()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::DRAFT_UUID.'/order');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Continuer')->form(), [
            'printing_order_buy[billingOrganization]' => 'CPGT SAS',
            'printing_order_buy[billingEmail]' => 'billing@citipo.com',
            'printing_order_buy[billingStreetLine1]' => '48 Rue de Ponthieu',
            'printing_order_buy[billingStreetLine2]' => 'Etage 1',
            'printing_order_buy[billingPostalCode]' => '75008',
            'printing_order_buy[billingCity]' => 'Paris',
            'printing_order_buy[billingCountry]' => 'FR',
            'printing_order_buy[recipientFirstName]' => 'Titouan',
            'printing_order_buy[recipientLastName]' => 'Galopin',
            'printing_order_buy[recipientEmail]' => 'titouan.galopin@citipo.com',
        ]);
        $this->assertResponseRedirects();

        // Check order
        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_UUID]);
        $this->assertSame(['payment_pending' => 1, 'bat_pending' => 1], $order->getStatus());

        // Check organization details
        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_ACME_UUID);
        $this->assertSame('CPGT SAS', $orga->getBillingName());
        $this->assertSame('billing@citipo.com', $orga->getBillingEmail());
        $this->assertSame('48 Rue de Ponthieu', $orga->getBillingAddressStreetLine1());
        $this->assertSame('Etage 1', $orga->getBillingAddressStreetLine2());
        $this->assertSame('75008', $orga->getBillingAddressPostalCode());
        $this->assertSame('PARIS', $orga->getBillingAddressCity());
        $this->assertSame('FR', $orga->getBillingAddressCountry());

        // Check database order
        $billingOrder = $order->getOrder();
        $this->assertSame(self::ORGA_ACME_UUID, $billingOrder->getOrganization()->getUuid()->toRfc4122());
        $this->assertSame(OrderAction::PRINT, $billingOrder->getAction()->getType());
        $this->assertSame(self::DRAFT_UUID, $billingOrder->getAction()->getPayload()['orderUuid']);
        $this->assertSame('Titouan', $billingOrder->getRecipient()->getFirstName());
        $this->assertSame('Galopin', $billingOrder->getRecipient()->getLastName());
        $this->assertSame('titouan.galopin@citipo.com', $billingOrder->getRecipient()->getEmail());
        $this->assertNotNull($mollieOrderId = $billingOrder->getMollieId());

        // Check customer
        /** @var MockMollie $mollie */
        $mollie = static::getContainer()->get(MollieInterface::class);
        $this->assertArrayHasKey('cst_DKnSArGRCm', $mollie->customers);

        /** @var Customer $customer */
        $customer = $mollie->customers['cst_DKnSArGRCm'];
        $this->assertSame('CPGT SAS', $customer->name);
        $this->assertSame('billing@citipo.com', $customer->email);
        $this->assertEquals(
            (object) [
                'uuid' => self::ORGA_ACME_UUID,
                'streetLine1' => '48 Rue de Ponthieu',
                'streetLine2' => 'Etage 1',
                'postalCode' => '75008',
                'city' => 'PARIS',
                'country' => 'FR',
            ],
            $customer->metadata
        );

        // Check order
        $this->assertArrayHasKey($mollieOrderId, $mollie->orders);

        /** @var MollieOrder $mollieOrder */
        $mollieOrder = $mollie->orders[$mollieOrderId];
        $this->assertNotNull($mollieOrder->orderNumber);
        $this->assertSame('EUR', $mollieOrder->amount->currency);
        $this->assertSame('1095.95', $mollieOrder->amount->value);
        $this->assertSame('CPGT SAS', $mollieOrder->billingAddress->organizationName);
        $this->assertSame('48 Rue de Ponthieu', $mollieOrder->billingAddress->streetAndNumber);
        $this->assertSame('PARIS', $mollieOrder->billingAddress->city);
        $this->assertSame('75008', $mollieOrder->billingAddress->postalCode);
        $this->assertSame('FR', $mollieOrder->billingAddress->country);
        $this->assertSame('Titouan', $mollieOrder->billingAddress->givenName);
        $this->assertSame('Galopin', $mollieOrder->billingAddress->familyName);
        $this->assertSame('titouan.galopin@citipo.com', $mollieOrder->billingAddress->email);
        $this->assertNotNull($mollieOrder->redirectUrl);
        $this->assertNotNull($mollieOrder->webhookUrl);
        $this->assertSame('banktransfer', $mollieOrder->method);

        // Check response
        $this->assertResponseRedirects('https://mollie.com/checkout');

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_printing');
        $this->assertCount(1, $messages = $transport->get());
        /* @var RequestCampaignPreflightMessage $message */
        $this->assertInstanceOf(RequestCampaignPreflightMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($order->getCampaigns()->first()->getId(), $message->getCampaignId());

        // Test the dispatching of the mail
        $this->assertCount(1, $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get());

        /* @var SendEmailMessage $message */
        $this->assertInstanceOf(SendEmailMessage::class, $message = $messages[0]->getMessage());
        $this->assertEmailHeaderSame($message->getMessage(), 'To', 'titouan.galopin@citipo.com');
        $this->assertEmailHeaderSame($message->getMessage(), 'Subject', '[Citipo] Nous avons bien reçu votre commande n°7E3617E3');
    }
}
