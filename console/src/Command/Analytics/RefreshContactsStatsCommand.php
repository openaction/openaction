<?php

namespace App\Command\Analytics;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Repository\OrganizationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:analytics:contacts:refresh',
    description: 'Refresh all contacts stats.',
)]
class RefreshContactsStatsCommand extends Command
{
    private OrganizationRepository $orgaRepo;
    private MessageBusInterface $bus;

    public function __construct(OrganizationRepository $orgaRepo, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->orgaRepo = $orgaRepo;
        $this->bus = $bus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->orgaRepo->findAll() as $orga) {
            if ($io->isVerbose()) {
                $io->writeln('Dispatching organization '.$orga->getName());
            }

            $this->bus->dispatch(new RefreshContactStatsMessage($orga->getId()));
        }

        $io->success('Stats refresh messages dispatched.');

        return Command::SUCCESS;
    }
}
