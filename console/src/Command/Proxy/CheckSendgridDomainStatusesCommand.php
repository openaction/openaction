<?php

namespace App\Command\Proxy;

use App\Proxy\Consumer\SendgridCheckDomainMessage;
use App\Repository\DomainRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:proxy:check-sendgrid',
    description: 'Dispatch domains statuses check messages for Sendgrid.',
)]
class CheckSendgridDomainStatusesCommand extends Command
{
    private DomainRepository $repository;
    private MessageBusInterface $bus;

    public function __construct(DomainRepository $dr, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->repository = $dr;
        $this->bus = $bus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->repository->findDomainsWithoutStatus('sendgrid_ready') as $domain) {
            $this->bus->dispatch(new SendgridCheckDomainMessage($domain->getId()));
        }

        $io->success('Messages dispatched.');

        return Command::SUCCESS;
    }
}
