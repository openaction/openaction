<?php

namespace App\Tests\Controller\Console\Project\Community\Printing;

use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingOrder;
use App\Platform\Products;
use App\Repository\Community\PrintingOrderRepository;
use App\Tests\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CreateControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/create');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Ajouter au panier')->form(), [
            'create_printing_campaign[product]' => Products::PRINT_CAMPAIGN_FLYER,
        ]);
        $this->assertResponseRedirects();

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy([], ['createdAt' => 'DESC']);
        self::getContainer()->get(EntityManagerInterface::class)->refresh($order);

        $this->assertNull($order->getDeliveryMainAddressName());
        $this->assertNull($order->getDeliveryMainAddressStreet1());
        $this->assertNull($order->getDeliveryMainAddressStreet2());
        $this->assertNull($order->getDeliveryMainAddressZipCode());
        $this->assertNull($order->getDeliveryMainAddressCity());
        $this->assertNull($order->getDeliveryMainAddressCountry());
        $this->assertNull($order->getDeliveryMainAddressInstructions());
        $this->assertNull($order->getDeliveryPosterAddressName());
        $this->assertNull($order->getDeliveryPosterAddressStreet1());
        $this->assertNull($order->getDeliveryPosterAddressStreet2());
        $this->assertNull($order->getDeliveryPosterAddressZipCode());
        $this->assertNull($order->getDeliveryPosterAddressCity());
        $this->assertNull($order->getDeliveryPosterAddressCountry());
        $this->assertNull($order->getDeliveryPosterAddressInstructions());
        $this->assertCount(1, $campaigns = $order->getCampaigns());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns->first());
        $this->assertSame(Products::PRINT_CAMPAIGN_FLYER, $campaign->getProduct());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testCreateKit()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/create');
        $this->assertResponseIsSuccessful();

        $client->clickLink('Commander le kit "Propagande officielle"');
        $this->assertResponseRedirects();

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy([], ['createdAt' => 'DESC']);
        self::getContainer()->get(EntityManagerInterface::class)->refresh($order);

        $this->assertNull($order->getDeliveryMainAddressName());
        $this->assertNull($order->getDeliveryMainAddressStreet1());
        $this->assertNull($order->getDeliveryMainAddressStreet2());
        $this->assertNull($order->getDeliveryMainAddressZipCode());
        $this->assertNull($order->getDeliveryMainAddressCity());
        $this->assertNull($order->getDeliveryMainAddressCountry());
        $this->assertNull($order->getDeliveryMainAddressInstructions());
        $this->assertNull($order->getDeliveryPosterAddressName());
        $this->assertNull($order->getDeliveryPosterAddressStreet1());
        $this->assertNull($order->getDeliveryPosterAddressStreet2());
        $this->assertNull($order->getDeliveryPosterAddressZipCode());
        $this->assertNull($order->getDeliveryPosterAddressCity());
        $this->assertNull($order->getDeliveryPosterAddressCountry());
        $this->assertNull($order->getDeliveryPosterAddressInstructions());
        $this->assertCount(4, $campaigns = $order->getCampaigns());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns[0]);
        $this->assertSame(Products::PRINT_OFFICIAL_POSTER, $campaign->getProduct());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns[1]);
        $this->assertSame(Products::PRINT_OFFICIAL_BANNER, $campaign->getProduct());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns[2]);
        $this->assertSame(Products::PRINT_OFFICIAL_PLEDGE, $campaign->getProduct());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns[3]);
        $this->assertSame(Products::PRINT_OFFICIAL_BALLOT, $campaign->getProduct());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testAddOrder()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/create');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Ajouter au panier')->form(), [
            'create_printing_campaign[product]' => Products::PRINT_CAMPAIGN_FLYER,
        ]);
        $this->assertResponseRedirects();

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy([], ['createdAt' => 'DESC']);
        self::getContainer()->get(EntityManagerInterface::class)->refresh($order);

        $this->assertNull($order->getDeliveryMainAddressName());
        $this->assertNull($order->getDeliveryMainAddressStreet1());
        $this->assertNull($order->getDeliveryMainAddressStreet2());
        $this->assertNull($order->getDeliveryMainAddressZipCode());
        $this->assertNull($order->getDeliveryMainAddressCity());
        $this->assertNull($order->getDeliveryMainAddressCountry());
        $this->assertNull($order->getDeliveryMainAddressInstructions());
        $this->assertNull($order->getDeliveryPosterAddressName());
        $this->assertNull($order->getDeliveryPosterAddressStreet1());
        $this->assertNull($order->getDeliveryPosterAddressStreet2());
        $this->assertNull($order->getDeliveryPosterAddressZipCode());
        $this->assertNull($order->getDeliveryPosterAddressCity());
        $this->assertNull($order->getDeliveryPosterAddressCountry());
        $this->assertNull($order->getDeliveryPosterAddressInstructions());
        $this->assertCount(1, $campaigns = $order->getCampaigns());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns->first());
        $this->assertSame(Products::PRINT_CAMPAIGN_FLYER, $campaign->getProduct());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
