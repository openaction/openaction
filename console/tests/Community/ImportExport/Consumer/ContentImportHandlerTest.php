<?php

namespace App\Tests\Community\ImportExport\Consumer;

use App\Community\ImportExport\Consumer\ContentImportHandler;
use App\Community\ImportExport\Consumer\ContentImportMessage;
use App\Entity\Community\ContentImport;
use App\Repository\Community\ContentImportRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

class ContentImportHandlerTest extends KernelTestCase
{
    public function testConsumeInvalid(): void
    {
        self::bootKernel();

        $handler = static::getContainer()->get(ContentImportHandler::class);
        $handler(new ContentImportMessage(0));

        /** @var ReceiverInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(0, $messages);
    }

    public function testConsumeAlreadyStarted(): void
    {
        self::bootKernel();

        /** @var ContentImport $import */
        $import = static::getContainer()->get(ContentImportRepository::class)->findOneByUuid('8a7f9d2e-56c1-4826-9b40-7fe8a58e3d14');
        $this->assertInstanceOf(ContentImport::class, $import);


    }
}