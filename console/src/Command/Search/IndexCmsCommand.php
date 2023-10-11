<?php

namespace App\Command\Search;

use App\Search\CmsIndexer;
use App\Util\Json;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:search:index-cms',
    description: 'Index the CMS database in Meilisearch.',
)]
class IndexCmsCommand extends Command
{
    public function __construct(private readonly CmsIndexer $cmsIndexer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $documents = [];

        /*
         * Pages
         */

        $io->write('Creating pages documents... ');

        $count = 0;
        foreach ($this->cmsIndexer->createPagesDocuments() as $document) {
            $documents[] = Json::encode($document);
            ++$count;
        }

        $io->writeln($count.' pages to index');

        /*
         * Posts
         */

        $io->write('Creating posts documents... ');

        $count = 0;
        foreach ($this->cmsIndexer->createPostsDocuments() as $document) {
            $documents[] .= Json::encode($document);
            ++$count;
        }

        $io->writeln($count.' posts to index');

        /*
         * Events
         */

        $io->write('Creating events documents... ');

        $count = 0;
        foreach ($this->cmsIndexer->createEventsDocuments() as $document) {
            $documents[] .= Json::encode($document);
            ++$count;
        }

        $io->writeln($count.' events to index');

        /*
         * Indexing
         */

        $io->writeln('Clearing current index... ');
        $this->cmsIndexer->clearIndex();

        $io->writeln('Recreate index... ');
        $this->cmsIndexer->createIndex();

        $io->writeln('Indexing documents... ');
        $this->cmsIndexer->indexDocuments($documents);

        $io->success('Indexing done');

        return Command::SUCCESS;
    }
}
