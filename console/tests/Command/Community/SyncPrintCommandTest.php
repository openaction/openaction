<?php

namespace App\Tests\Command\Community;

use App\Community\Printing\Consumer\ImportPreflightResultMessage;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SyncPrintCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $application = new Application(static::bootKernel());

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('lgp.storage');
        $storage->write('Preflight/OUT/TODO/1d73e638-650a-4c38-8f01-16e0bb0fb361/bat.xml', '');
        $storage->write('Preflight/OUT/TODO/f5dde871-da58-40bc-a951-97f3e7557f7c/bat.xml', '');

        $commandTester = new CommandTester($application->find('app:community:sync-print'));
        $commandTester->execute([]);
        $this->assertStringContainsString('[OK] Sync messages dispatched.', $commandTester->getDisplay());

        // Test the dispatching of the messages
        $transport = static::getContainer()->get('messenger.transport.async_printing');
        $this->assertCount(2, $messages = $transport->get());

        /* @var ImportPreflightResultMessage $message */
        $this->assertInstanceOf(ImportPreflightResultMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame('1d73e638-650a-4c38-8f01-16e0bb0fb361', $message->getResultUuid());
        $this->assertInstanceOf(ImportPreflightResultMessage::class, $message = $messages[1]->getMessage());
        $this->assertSame('f5dde871-da58-40bc-a951-97f3e7557f7c', $message->getResultUuid());
    }
}
