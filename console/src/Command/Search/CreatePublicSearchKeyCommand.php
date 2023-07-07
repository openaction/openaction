<?php

namespace App\Command\Search;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Search\CmsIndexer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:search:create-cms-key',
    description: 'Create a public search key for the CMS.',
)]
class CreatePublicSearchKeyCommand extends Command
{
    public function __construct(private readonly MeilisearchInterface $meilisearch)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('Creating API key... ');

        $searchKey = $this->meilisearch->createApiKey([CmsIndexer::INDEX_NAME], ['search', 'documents.get', 'stats.get']);

        $io->success('Public search key: '.$searchKey->getKey());

        return Command::SUCCESS;
    }
}
