<?php

namespace App\Command\Search;

use App\Bridge\Meilisearch\MeilisearchInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:search:clean',
    description: 'Clean unused legacy indexes from Meilisearch.',
)]
class CleanLegacyIndexesCommand extends Command
{
    public function __construct(private readonly MeilisearchInterface $meilisearch)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $perOrga = [];
        foreach ($this->meilisearch->listIndexes() as $index) {
            if (!str_starts_with($index, 'crm_')) {
                continue;
            }

            $parts = explode('_', $index);
            if (3 !== count($parts)) {
                continue;
            }

            $perOrga[$parts[1]][] = $parts[2];
        }

        // Remove duplicate indexes that are older than 2 days ago (to account for indexes being built right now),
        // except the last one (as it is currently active)
        foreach ($perOrga as $orgaUuid => $indexes) {
            if (1 === count($indexes)) {
                continue;
            }

            $dates = [];
            foreach ($indexes as $index) {
                $date = substr($index, 0, 10);
                $dates[$date] = $index;
            }

            $twoDaysAgo = (new \DateTime('2 days ago'))->format('Y-m-d');
            $lastIndexDate = max(array_keys($dates));

            foreach ($dates as $date => $index) {
                if ($date !== $lastIndexDate && $date < $twoDaysAgo) {
                    $io->writeln('Removing index crm_'.$orgaUuid.'_'.$index);
                    $this->meilisearch->deleteIndex('crm_'.$orgaUuid.'_'.$index);
                }
            }
        }

        $io->success('Cleaning done');

        return Command::SUCCESS;
    }
}
