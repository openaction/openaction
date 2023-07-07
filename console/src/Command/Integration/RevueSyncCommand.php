<?php

namespace App\Command\Integration;

use App\Bridge\Revue\Consumer\RevueSyncMessage;
use App\Repository\Integration\RevueAccountRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:integration:revue-sync',
    description: 'Synchronize Twitter Revue accounts subscribers with Citipo organizations.',
)]
class RevueSyncCommand extends Command
{
    private RevueAccountRepository $repository;
    private MessageBusInterface $bus;

    public function __construct(RevueAccountRepository $repository, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->addOption('organization-uuid', 'o', InputOption::VALUE_REQUIRED, 'Limit synchronization to a single organization.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->repository->findToSync() as $account) {
            if ($io->isVerbose()) {
                $io->write('Dispatching for account #'.$account->getLabel().' ('.$account->getOrganization()->getName().')...');
            }

            $this->bus->dispatch(new RevueSyncMessage($account->getId()));

            if ($io->isVerbose()) {
                $io->writeln(' OK');
            }
        }

        $io->success('Sync messages dispatched.');

        return Command::SUCCESS;
    }
}
