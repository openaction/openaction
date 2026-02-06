<?php

namespace App\Command\Analytics;

use App\Repository\Analytics\Website\SessionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:analytics:sessions:clear',
    description: 'Clear session stats older than 3 years ago.',
)]
class ClearWebsitesSessionsCommand extends Command
{
    public function __construct(
        private readonly SessionRepository $sessionRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Clearing ...');

        $this->sessionRepository->removeOldSessions('3 years ago');

        $io->success('Sessions clear messages dispatched.');

        return Command::SUCCESS;
    }
}
