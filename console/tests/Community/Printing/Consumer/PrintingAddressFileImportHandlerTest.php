<?php

namespace App\Tests\Community\Printing\Consumer;

use App\Community\Printing\Consumer\ImportPrintingAddressFileHandler;
use App\Community\Printing\Consumer\ImportPrintingAddressFileMessage;
use App\Entity\Community\PrintingOrder;
use App\Repository\Community\PrintingOrderRepository;
use App\Tests\KernelTestCase;
use League\Flysystem\FilesystemOperator;

class PrintingAddressFileImportHandlerTest extends KernelTestCase
{
    private const DRAFT_NO_FILE_UUID = '7e3617e3-b147-4f53-864c-1550d65ddbc4';
    private const DRAFT_FILE_UUID = '4730f1df-b3ba-4b2b-8d08-b459888e760d';

    public function testConsumeInvalid()
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ImportPrintingAddressFileHandler::class);

        // Shouldn't fail
        $this->assertTrue($handler(new ImportPrintingAddressFileMessage(0, [])));
    }

    public function testConsumeNoFile()
    {
        self::bootKernel();

        /** @var PrintingOrder $order */
        $order = static::getContainer()->get(PrintingOrderRepository::class)->findOneByUuid(self::DRAFT_NO_FILE_UUID);
        $this->assertInstanceOf(PrintingOrder::class, $order);

        $handler = static::getContainer()->get(ImportPrintingAddressFileHandler::class);
        $handler(new ImportPrintingAddressFileMessage($order->getId(), []));

        // Shouldn't have done anything
        /** @var PrintingOrder $order */
        $order = static::getContainer()->get(PrintingOrderRepository::class)->findOneByUuid(self::DRAFT_NO_FILE_UUID);
        $this->assertInstanceOf(PrintingOrder::class, $order);

        $this->assertNull($order->getDeliveryAddressList());
    }

    public function testConsumeValid()
    {
        self::bootKernel();

        /** @var PrintingOrder $order */
        $order = static::getContainer()->get(PrintingOrderRepository::class)->findOneByUuid(self::DRAFT_FILE_UUID);
        $this->assertInstanceOf(PrintingOrder::class, $order);

        /** @var FilesystemOperator $storage */
        $storage = self::getContainer()->get('cdn.storage');

        // Populate uploaded file with real file
        $storage->write('print-addressed.xlsx', file_get_contents(__DIR__.'/../../../Fixtures/printing/addresses.xlsx'));

        $handler = static::getContainer()->get(ImportPrintingAddressFileHandler::class);
        $handler(new ImportPrintingAddressFileMessage($order->getId(), [
            'firstName',
            'lastName',
            'street1',
            'zipCode',
            'city',
            'ignored',
            'ignored',
        ]));

        // Should have created the addresses list
        /** @var PrintingOrder $order */
        $order = static::getContainer()->get(PrintingOrderRepository::class)->findOneByUuid(self::DRAFT_FILE_UUID);
        $this->assertInstanceOf(PrintingOrder::class, $order);
        $this->assertSame(
            [
                [
                    'formalTitle' => null,
                    'firstName' => 'Titouan',
                    'lastName' => 'Galopin',
                    'street1' => '49 Rue de Ponthieu',
                    'street2' => null,
                    'zipCode' => '75008',
                    'city' => 'Paris',
                    'country' => null,
                ],
                [
                    'formalTitle' => null,
                    'firstName' => 'Adrien',
                    'lastName' => 'Duguet',
                    'street1' => '50 Rue de Ponthieu',
                    'street2' => null,
                    'zipCode' => '75008',
                    'city' => 'Paris',
                    'country' => null,
                ],
                [
                    'formalTitle' => null,
                    'firstName' => 'Jean',
                    'lastName' => 'Martin',
                    'street1' => '51 Rue de Ponthieu',
                    'street2' => null,
                    'zipCode' => '75008',
                    'city' => 'Paris',
                    'country' => null,
                ],
            ],
            $order->getDeliveryAddressList(),
        );

        foreach ($order->getCampaigns() as $campaign) {
            $this->assertSame(3, $campaign->getQuantity());
        }

        // Should have removed the file
        $this->assertNull($order->getDeliveryAddressFile());
        $this->assertNull($order->getDeliveryAddressFileFirstLines());
        $this->assertFalse($storage->fileExists('print-addressed.xlsx'));
    }
}
