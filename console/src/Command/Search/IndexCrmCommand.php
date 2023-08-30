<?php

namespace App\Command\Search;

use App\Repository\OrganizationRepository;
use App\Search\CrmIndexer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:search:index-crm',
    description: 'Index the CRM database in MeiliSearch.',
)]
class IndexCrmCommand extends Command
{
    public function __construct(private CrmIndexer $crmIndexer, private OrganizationRepository $organizationRepo)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption('organization-uuid', null, InputOption::VALUE_REQUIRED, 'An optional organization UUID to limit indexing to this organization')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $orgaId = null;
        $orgaUuid = null;

        if ($uuid = $input->getOption('organization-uuid')) {
            if (!$organization = $this->organizationRepo->findOneByUuid($uuid)) {
                throw new \InvalidArgumentException('Organization with UUID '.$uuid.' not found');
            }

            $orgaId = $organization->getId();
            $orgaUuid = $organization->getUuid()->toRfc4122();
        }

        /*
         * Creating indexing table
         */
        $io->write('Resetting temporary table... ');
        $this->crmIndexer->resetIndexingTable();
        $io->writeln('OK');

        $io->write('Populating temporary table... ');
        if ($orgaId) {
            $this->crmIndexer->populateIndexingTableForOrganization($orgaId);
        } else {
            $this->crmIndexer->populateIndexingTableForAllOrganizations();
        }
        $io->writeln('OK');

        /*
         * Dumping indexing data locally
         */
        $io->write('Dumping to file... ');
        $dumpedFilename = $this->crmIndexer->dumpIndexingTableToFile();
        $io->writeln('OK');

        /*
         * Preparing Meilisearch uploads
         */
        $io->write('Creating ndjson batches... ');

        $batches = [];

        if ($orgaUuid) {
            $batches[$orgaUuid] = $this->crmIndexer->createNdJsonBatchesFromFile($dumpedFilename)[$orgaUuid] ?? [];
        } else {
            // Create empty batches to ensure the creation of an index even without contacts
            foreach ($this->organizationRepo->findAllUuids() as $uuid) {
                $batches[(string) $uuid] = [];
            }

            // Override with actual batches for organizations with contacts
            foreach ($this->crmIndexer->createNdJsonBatchesFromFile($dumpedFilename) as $uuid => $filenames) {
                $batches[$uuid] = $filenames;
            }
        }

        $io->writeln('OK');

        /*
         * Indexing
         */
        $io->writeln('Indexing organizations contacts...');

        $progress = new ProgressBar($output, count($batches) * 4);

        foreach ($batches as $orgaUuid => $filenames) {
            // Create new index version
            $newVersion = $this->crmIndexer->createIndexVersion($orgaUuid);
            $progress->advance();

            // Upload ndjson files
            $tasks = [];
            foreach ($filenames as $file) {
                $tasks[] = $this->crmIndexer->indexFile($orgaUuid, $newVersion, $file);
            }

            // Wait for indexing to finish
            if ($tasks) {
                $this->crmIndexer->waitForIndexing($tasks);
            }
            $progress->advance();

            // Create organization members search keys and swap live index version
            $this->crmIndexer->bumpIndexVersion($orgaUuid, $newVersion);
            $progress->advance();
        }

        $progress->finish();
        $output->write("\n");

        $io->success('Indexing done');

        return Command::SUCCESS;
    }
}
