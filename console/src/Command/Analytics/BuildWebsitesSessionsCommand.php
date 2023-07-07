<?php

namespace App\Command\Analytics;

use App\Analytics\Consumer\BuildWebsiteSessionsMessage;
use App\Repository\ProjectRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:analytics:sessions:build',
    description: 'Build session aggregates from page views.',
)]
class BuildWebsitesSessionsCommand extends Command
{
    private ProjectRepository $projectRepo;
    private MessageBusInterface $bus;

    public function __construct(ProjectRepository $projectRepo, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->projectRepo = $projectRepo;
        $this->bus = $bus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->projectRepo->findWebsiteProjectsIds() as $id) {
            if ($io->isVerbose()) {
                $io->writeln('Dispatching for project '.$id);
            }

            $this->bus->dispatch(new BuildWebsiteSessionsMessage($id));
        }

        $io->success('Sessions build messages dispatched.');

        return Command::SUCCESS;
    }
}
