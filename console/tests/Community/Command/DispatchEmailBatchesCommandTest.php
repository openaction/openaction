<?php

namespace App\Tests\Community\Command;

use App\Bridge\Postmark\Consumer\PostmarkMessage;
use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Command\Community\DispatchEmailBatchesCommand;
use App\Entity\Community\EmailBatch;
use App\Repository\Community\EmailBatchRepository;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class DispatchEmailBatchesCommandTest extends KernelTestCase
{
    public function testDispatchesDueBatches(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $dueSendgrid = new EmailBatch('campaign:1', 'sendgrid', ['example' => true]);
        $dueSendgrid->setScheduledAt(new \DateTime('-10 minutes'));
        $em->persist($dueSendgrid);

        $duePostmark = new EmailBatch('campaign:2', 'postmark', ['example' => true]);
        $duePostmark->setScheduledAt(new \DateTime('-5 minutes'));
        $em->persist($duePostmark);

        $future = new EmailBatch('campaign:3', 'sendgrid', ['example' => true]);
        $future->setScheduledAt(new \DateTime('+30 minutes'));
        $em->persist($future);

        $automation = new EmailBatch('automation:1', 'sendgrid', ['example' => true]);
        $automation->setScheduledAt(new \DateTime('-5 minutes'));
        $em->persist($automation);

        $em->flush();

        $dueSendgridId = $dueSendgrid->getId();
        $duePostmarkId = $duePostmark->getId();
        $futureId = $future->getId();
        $automationId = $automation->getId();

        /** @var InMemoryTransport $transport */
        $transport = static::getContainer()->get('messenger.transport.async_emailing');
        $transport->reset();

        $command = static::getContainer()->get(DispatchEmailBatchesCommand::class);
        $tester = new CommandTester($command);
        $tester->execute([]);

        $messages = $transport->get();
        $this->assertCount(2, $messages);

        $messageClasses = array_map(static fn ($envelope) => $envelope->getMessage()::class, $messages);
        sort($messageClasses);
        $this->assertSame([PostmarkMessage::class, SendgridMessage::class], $messageClasses);

        $em->clear();

        /** @var EmailBatchRepository $repository */
        $repository = static::getContainer()->get(EmailBatchRepository::class);

        $dueSendgrid = $repository->find($dueSendgridId);
        $this->assertNotNull($dueSendgrid->getQueuedAt());

        $duePostmark = $repository->find($duePostmarkId);
        $this->assertNotNull($duePostmark->getQueuedAt());

        $future = $repository->find($futureId);
        $this->assertNull($future->getQueuedAt());

        $automation = $repository->find($automationId);
        $this->assertNull($automation->getQueuedAt());
    }
}
