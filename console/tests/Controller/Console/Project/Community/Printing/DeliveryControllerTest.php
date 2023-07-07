<?php

namespace App\Tests\Controller\Console\Project\Community\Printing;

use App\Community\Printing\Consumer\ImportPrintingAddressFileMessage;
use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingOrder;
use App\Entity\Upload;
use App\Platform\Products;
use App\Repository\Community\PrintingOrderRepository;
use App\Tests\WebTestCase;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DeliveryControllerTest extends WebTestCase
{
    private const DRAFT_UUID = '7e3617e3-b147-4f53-864c-1550d65ddbc4';
    private const DRAFT_TO_MATCH_UUID = '4730f1df-b3ba-4b2b-8d08-b459888e760d';
    private const DRAFT_POSTER_UUID = '9557ff76-88a0-42aa-9c08-c4ce31e5e8a6';

    public function testDeliveryUnaddressedMediapost()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_UUID]);
        $this->assertCount(1, $campaigns = $order->getCampaigns());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns->first());
        $this->assertSame(2, $campaign->getQuantity());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::DRAFT_UUID.'/delivery');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="printing_order_unaddressed_delivery"]')->form(), [
            'printing_order_unaddressed_delivery[useMediapost]' => true,
            'printing_order_unaddressed_delivery[withEnveloping]' => false,
            'printing_order_unaddressed_delivery[addressName]' => 'Mediapost',
            'printing_order_unaddressed_delivery[addressStreet1]' => '49 Rue de Ponthieu',
            'printing_order_unaddressed_delivery[addressStreet2]' => 'Etage 1',
            'printing_order_unaddressed_delivery[addressZipCode]' => '75008',
            'printing_order_unaddressed_delivery[addressCity]' => 'Paris',
            'printing_order_unaddressed_delivery[addressCountry]' => 'FR',
            'printing_order_unaddressed_delivery[addressInstructions]' => 'Instructions',
            'printing_order_unaddressed_delivery[quantities]['.$campaign->getId().']' => 3000,
        ]);
        $this->assertResponseRedirects();

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_UUID]);
        $this->assertFalse($order->isDeliveryAddressed());
        $this->assertTrue($order->isDeliveryUseMediapost());
        $this->assertFalse($order->isWithEnveloping());
        $this->assertTrue($order->isDeliveryReadyToOrder());
        $this->assertSame('Mediapost', $order->getDeliveryMainAddressName());
        $this->assertSame('49 Rue de Ponthieu', $order->getDeliveryMainAddressStreet1());
        $this->assertSame('Etage 1', $order->getDeliveryMainAddressStreet2());
        $this->assertSame('75008', $order->getDeliveryMainAddressZipCode());
        $this->assertSame('PARIS', $order->getDeliveryMainAddressCity());
        $this->assertSame('FR', $order->getDeliveryMainAddressCountry());
        $this->assertSame('Instructions', $order->getDeliveryMainAddressInstructions());
        $this->assertNull($order->getDeliveryAddressFile());
        $this->assertNull($order->getDeliveryAddressList());
        $this->assertSame(3000, $order->getCampaigns()->first()->getQuantity());
    }

    public function testDeliveryUnaddressedIndividual()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_UUID]);
        $this->assertCount(1, $campaigns = $order->getCampaigns());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns->first());
        $this->assertSame(2, $campaign->getQuantity());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::DRAFT_UUID.'/delivery');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="printing_order_unaddressed_delivery"]')->form(), [
            'printing_order_unaddressed_delivery[useMediapost]' => false,
            'printing_order_unaddressed_delivery[withEnveloping]' => true,
            'printing_order_unaddressed_delivery[addressName]' => 'Titouan Galopin',
            'printing_order_unaddressed_delivery[addressStreet1]' => '49 Rue de Ponthieu',
            'printing_order_unaddressed_delivery[addressStreet2]' => 'Etage 1',
            'printing_order_unaddressed_delivery[addressZipCode]' => '75008',
            'printing_order_unaddressed_delivery[addressCity]' => 'Paris',
            'printing_order_unaddressed_delivery[addressCountry]' => 'FR',
            'printing_order_unaddressed_delivery[addressInstructions]' => 'Instructions',
            'printing_order_unaddressed_delivery[quantities]['.$campaign->getId().']' => 3000,
        ]);
        $this->assertResponseRedirects();

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_UUID]);
        $this->assertFalse($order->isDeliveryAddressed());
        $this->assertFalse($order->isDeliveryUseMediapost());
        $this->assertFalse($order->isWithEnveloping());
        $this->assertTrue($order->isDeliveryReadyToOrder());
        $this->assertSame('Titouan Galopin', $order->getDeliveryMainAddressName());
        $this->assertSame('49 Rue de Ponthieu', $order->getDeliveryMainAddressStreet1());
        $this->assertSame('Etage 1', $order->getDeliveryMainAddressStreet2());
        $this->assertSame('75008', $order->getDeliveryMainAddressZipCode());
        $this->assertSame('PARIS', $order->getDeliveryMainAddressCity());
        $this->assertSame('FR', $order->getDeliveryMainAddressCountry());
        $this->assertSame('Instructions', $order->getDeliveryMainAddressInstructions());
        $this->assertNull($order->getDeliveryAddressFile());
        $this->assertNull($order->getDeliveryAddressList());
        $this->assertSame(3000, $order->getCampaigns()->first()->getQuantity());
    }

    public function testDeliveryUnaddressedPosters()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_POSTER_UUID]);
        $this->assertCount(1, $campaigns = $order->getCampaigns());
        $this->assertTrue($order->isDeliveryReadyToOrder());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $campaigns->first());
        $this->assertSame(4, $campaign->getQuantity());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::DRAFT_POSTER_UUID.'/delivery');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="printing_order_unaddressed_delivery"]')->form(), [
            'printing_order_unaddressed_delivery[posterAddressName]' => 'Titouan Galopin',
            'printing_order_unaddressed_delivery[posterAddressStreet1]' => '49 Rue de Ponthieu',
            'printing_order_unaddressed_delivery[posterAddressStreet2]' => 'Etage 1',
            'printing_order_unaddressed_delivery[posterAddressZipCode]' => '75008',
            'printing_order_unaddressed_delivery[posterAddressCity]' => 'Paris',
            'printing_order_unaddressed_delivery[posterAddressCountry]' => 'FR',
            'printing_order_unaddressed_delivery[posterAddressInstructions]' => 'Instructions',
            'printing_order_unaddressed_delivery[quantities]['.$campaign->getId().']' => 450,
        ]);
        $this->assertResponseRedirects();

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_POSTER_UUID]);
        $this->assertFalse($order->isDeliveryAddressed());
        $this->assertFalse($order->isDeliveryUseMediapost());
        $this->assertTrue($order->isDeliveryReadyToOrder());
        $this->assertNull($order->getDeliveryMainAddressName());
        $this->assertSame('49 Rue de Ponthieu', $order->getDeliveryMainAddressStreet1());
        $this->assertSame('CPGT SAS', $order->getDeliveryMainAddressStreet2());
        $this->assertSame('75008', $order->getDeliveryMainAddressZipCode());
        $this->assertSame('PARIS', $order->getDeliveryMainAddressCity());
        $this->assertSame('FR', $order->getDeliveryMainAddressCountry());
        $this->assertSame('Floor 1', $order->getDeliveryMainAddressInstructions());
        $this->assertSame('Titouan Galopin', $order->getDeliveryPosterAddressName());
        $this->assertSame('49 Rue de Ponthieu', $order->getDeliveryPosterAddressStreet1());
        $this->assertSame('Etage 1', $order->getDeliveryPosterAddressStreet2());
        $this->assertSame('75008', $order->getDeliveryPosterAddressZipCode());
        $this->assertSame('PARIS', $order->getDeliveryPosterAddressCity());
        $this->assertSame('FR', $order->getDeliveryPosterAddressCountry());
        $this->assertSame('Instructions', $order->getDeliveryPosterAddressInstructions());
        $this->assertNull($order->getDeliveryAddressFile());
        $this->assertNull($order->getDeliveryAddressList());
        $this->assertSame(450, $order->getCampaigns()->first()->getQuantity());
    }

    public function testDeliveryAddressedUpload()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::DRAFT_UUID.'/delivery');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="printing_order_addressed_delivery"]')->form(), [
            'printing_order_addressed_delivery[addressList]' => new UploadedFile(
                __DIR__.'/../../../../../Fixtures/printing/addresses.xlsx',
                'addresses.xlsx'
            ),
        ]);
        $this->assertResponseRedirects();

        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_UUID]);
        $this->assertTrue($order->isDeliveryAddressed());
        $this->assertInstanceOf(Upload::class, $order->getDeliveryAddressFile());
        $this->assertNull($order->getDeliveryAddressList());
        $this->assertTrue($order->isWithEnveloping());
        $this->assertFalse($order->isDeliveryUseMediapost());
        $this->assertFalse($order->isDeliveryReadyToOrder());
        $this->assertNull($order->getDeliveryMainAddressStreet1());
        $this->assertNull($order->getDeliveryMainAddressStreet2());
        $this->assertNull($order->getDeliveryMainAddressZipCode());
        $this->assertNull($order->getDeliveryMainAddressCity());
        $this->assertNull($order->getDeliveryMainAddressCountry());

        /* @var PrintingCampaign $campaign */
        $this->assertInstanceOf(PrintingCampaign::class, $campaign = $order->getCampaigns()->first());
        $this->assertSame(Products::PRINT_CAMPAIGN_DOOR, $campaign->getProduct());

        // Check the file exists
        /** @var FilesystemReader $storage */
        $storage = self::getContainer()->get('cdn.storage');
        $this->assertTrue($storage->fileExists($order->getDeliveryAddressFile()->getPathname()));
    }

    public function testColumns()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/'.self::DRAFT_TO_MATCH_UUID.'/delivery/columns');
        $this->assertResponseIsSuccessful();

        /** @var FilesystemOperator $storage */
        $storage = self::getContainer()->get('cdn.storage');

        // Populate uploaded file with real file
        $storage->write('print-addressed.xlsx', file_get_contents(__DIR__.'/../../../../../Fixtures/printing/addresses.xlsx'));

        $client->submit($crawler->filter('form[name="printing_order_address_file_columns"]')->form(), [
            'printing_order_address_file_columns[columnsTypes]' => [
                'firstName',
                'lastName',
                'street1',
                'zipCode',
                'city',
            ],
        ]);
        $this->assertResponseRedirects();

        // Check dispatching of the message
        /** @var PrintingOrder $order */
        $order = self::getContainer()->get(PrintingOrderRepository::class)->findOneBy(['uuid' => self::DRAFT_TO_MATCH_UUID]);

        $transport = self::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var ImportPrintingAddressFileMessage $message */
        $this->assertInstanceOf(ImportPrintingAddressFileMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($order->getId(), $message->getOrderId());
        $this->assertSame([
            'firstName',
            'lastName',
            'street1',
            'zipCode',
            'city',
            'ignored',
            'ignored',
        ], $message->getColumns());

        // Check processing loader
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
