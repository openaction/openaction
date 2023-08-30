<?php

namespace App\Tests\Command\Integration;

use App\Bridge\Revue\Consumer\RevueSyncMessage;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RevueSyncCommandTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $application = new Application(static::bootKernel());

        $commandTester = new CommandTester($application->find('app:integration:revue-sync'));
        $commandTester->execute([]);
        $this->assertStringContainsString('[OK] Sync messages dispatched.', $commandTester->getDisplay());

        // Test the dispatching of the messages
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(2, $messages = $transport->get());
        $this->assertInstanceOf(RevueSyncMessage::class, $messages[0]->getMessage());
        $this->assertInstanceOf(RevueSyncMessage::class, $messages[1]->getMessage());
    }
}
