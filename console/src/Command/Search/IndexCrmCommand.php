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

        $orgaUuid = null;

        if ($uuid = $input->getOption('organization-uuid')) {
            if (!$organization = $this->organizationRepo->findOneByUuid($uuid)) {
                throw new \InvalidArgumentException('Organization with UUID '.$uuid.' not found');
            }

            $orgaUuid = $organization->getUuid()->toRfc4122();
        }

        /*
         * Preparing Meilisearch uploads
         */
        $io->write('Creating ndjson batches... ');

        $batches = [];

        if ($orgaUuid) {
            $batches = $this->crmIndexer->createIndexingBatchesForOrganization($orgaUuid);
        } else {
            $allowedUuids = $this->organizationRepo->findUuidsForFullCrmReindex();

            if (!$allowedUuids) {
                $io->writeln('OK');
                $io->success('Indexing done (no organizations eligible for full reindex)');

                return Command::SUCCESS;
            }

            // Create empty batches to ensure the creation of an index even without contacts
            foreach ($allowedUuids as $uuid) {
                $batches[(string) $uuid] = [];
            }

            // Override with actual batches for organizations with contacts
            foreach ($this->crmIndexer->createIndexingBatchesForOrganizations($allowedUuids) as $uuid => $filenames) {
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
            foreach ($filenames as $batchFilename) {
                $tasks[] = $this->crmIndexer->indexBatch($orgaUuid, $newVersion, $batchFilename);
            }

            // Wait for indexing to finish
            if ($tasks) {
                $this->crmIndexer->waitForIndexing(array_filter($tasks));
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
