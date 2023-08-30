<?php

namespace App\Tests\Controller\Console\Organization\Community;

use App\Bridge\Mollie\MockMollie;
use App\Bridge\Mollie\MollieInterface;
use App\Entity\Billing\Model\OrderAction;
use App\Entity\Billing\Order;
use App\Entity\Organization;
use App\Repository\Billing\OrderRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Order as MollieOrder;
use Symfony\Component\HttpFoundation\Response;

class BuyCreditsControllerTest extends WebTestCase
{
    public function testBuyEmailCredits()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/buy-credits/emails');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Pay')->form(), [
            'buy_credits[amount]' => '2500',
            'buy_credits[billingOrganization]' => 'CPGT SAS',
            'buy_credits[billingEmail]' => 'billing@citipo.com',
            'buy_credits[billingStreetLine1]' => '48 Rue de Ponthieu',
            'buy_credits[billingStreetLine2]' => 'Etage 1',
            'buy_credits[billingPostalCode]' => '75008',
            'buy_credits[billingCity]' => 'Paris',
            'buy_credits[billingCountry]' => 'FR',
            'buy_credits[recipientFirstName]' => 'Titouan',
            'buy_credits[recipientLastName]' => 'Galopin',
            'buy_credits[recipientEmail]' => 'titouan.galopin@citipo.com',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Check organization details
        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_CITIPO_UUID);
        $this->assertSame('CPGT SAS', $orga->getBillingName());
        $this->assertSame('billing@citipo.com', $orga->getBillingEmail());
        $this->assertSame('48 Rue de Ponthieu', $orga->getBillingAddressStreetLine1());
        $this->assertSame('Etage 1', $orga->getBillingAddressStreetLine2());
        $this->assertSame('75008', $orga->getBillingAddressPostalCode());
        $this->assertSame('PARIS', $orga->getBillingAddressCity());
        $this->assertSame('FR', $orga->getBillingAddressCountry());

        // Check database order
        /** @var Order $order */
        $order = static::getContainer()->get(OrderRepository::class)->findOneBy([], ['createdAt' => 'DESC']);
        $this->assertSame(self::ORGA_CITIPO_UUID, $order->getOrganization()->getUuid()->toRfc4122());
        $this->assertSame(OrderAction::ADD_EMAIL_CREDITS, $order->getAction()->getType());
        $this->assertSame(2500, $order->getAction()->getPayload()['credits']);
        $this->assertSame('Titouan', $order->getRecipient()->getFirstName());
        $this->assertSame('Galopin', $order->getRecipient()->getLastName());
        $this->assertSame('titouan.galopin@citipo.com', $order->getRecipient()->getEmail());
        $this->assertNotNull($mollieOrderId = $order->getMollieId());

        // Check customer
        /** @var MockMollie $mollie */
        $mollie = static::getContainer()->get(MollieInterface::class);
        $this->assertArrayHasKey('cst_2c267514', $mollie->customers);

        /** @var Customer $customer */
        $customer = $mollie->customers['cst_2c267514'];
        $this->assertSame('CPGT SAS', $customer->name);
        $this->assertSame('billing@citipo.com', $customer->email);
        $this->assertEquals(
            (object) [
                'uuid' => self::ORGA_CITIPO_UUID,
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
        $this->assertSame('9.00', $mollieOrder->amount->value);
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
    }

    public function testBuyTextCredits()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/buy-credits/texts');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Pay')->form(), [
            'buy_credits[amount]' => '500',
            'buy_credits[billingOrganization]' => 'CPGT SAS',
            'buy_credits[billingEmail]' => 'billing@citipo.com',
            'buy_credits[billingStreetLine1]' => '48 Rue de Ponthieu',
            'buy_credits[billingStreetLine2]' => 'Etage 1',
            'buy_credits[billingPostalCode]' => '75008',
            'buy_credits[billingCity]' => 'Paris',
            'buy_credits[billingCountry]' => 'FR',
            'buy_credits[recipientFirstName]' => 'Titouan',
            'buy_credits[recipientLastName]' => 'Galopin',
            'buy_credits[recipientEmail]' => 'titouan.galopin@citipo.com',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Check organization details
        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_CITIPO_UUID);
        $this->assertSame('CPGT SAS', $orga->getBillingName());
        $this->assertSame('billing@citipo.com', $orga->getBillingEmail());
        $this->assertSame('48 Rue de Ponthieu', $orga->getBillingAddressStreetLine1());
        $this->assertSame('Etage 1', $orga->getBillingAddressStreetLine2());
        $this->assertSame('75008', $orga->getBillingAddressPostalCode());
        $this->assertSame('PARIS', $orga->getBillingAddressCity());
        $this->assertSame('FR', $orga->getBillingAddressCountry());

        // Check database order
        /** @var Order $order */
        $order = static::getContainer()->get(OrderRepository::class)->findOneBy([], ['createdAt' => 'DESC']);
        $this->assertSame(self::ORGA_CITIPO_UUID, $order->getOrganization()->getUuid()->toRfc4122());
        $this->assertSame(OrderAction::ADD_TEXT_CREDITS, $order->getAction()->getType());
        $this->assertSame(500, $order->getAction()->getPayload()['credits']);
        $this->assertSame('Titouan', $order->getRecipient()->getFirstName());
        $this->assertSame('Galopin', $order->getRecipient()->getLastName());
        $this->assertSame('titouan.galopin@citipo.com', $order->getRecipient()->getEmail());
        $this->assertNotNull($mollieOrderId = $order->getMollieId());

        // Check customer
        /** @var MockMollie $mollie */
        $mollie = static::getContainer()->get(MollieInterface::class);
        $this->assertArrayHasKey('cst_2c267514', $mollie->customers);

        /** @var Customer $customer */
        $customer = $mollie->customers['cst_2c267514'];
        $this->assertSame('CPGT SAS', $customer->name);
        $this->assertSame('billing@citipo.com', $customer->email);
        $this->assertEquals(
            (object) [
                'uuid' => self::ORGA_CITIPO_UUID,
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
        $this->assertSame('60.00', $mollieOrder->amount->value);
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
    }
}
