<?php

namespace App\Search\Consumer;

use App\Repository\OrganizationRepository;
use App\Search\CrmIndexer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ReindexOrganizationCrmHandler implements MessageHandlerInterface
{
    public function __construct(private OrganizationRepository $repository, private CrmIndexer $crmIndexer)
    {
    }

    public function __invoke(ReindexOrganizationCrmMessage $message)
    {
        if (!$orga = $this->repository->find($message->getOrganizationId())) {
            return;
        }

        $orgaUuid = $orga->getUuid()->toRfc4122();

        /*
         * Creating indexing table
         */
        $this->crmIndexer->resetIndexingTable();
        $this->crmIndexer->populateIndexingTableForOrganization($orga->getId());

        /*
         * Dumping indexing data locally
         */
        $dumpedFilename = $this->crmIndexer->dumpIndexingTableToFile();

        /*
         * Creating ndjson batches
         */
        // Create empty batches to ensure the creation of an index of organizations even without contacts
        $batches = [$orgaUuid => []];

        // Override with actual batches for organizations with contacts
        foreach ($this->crmIndexer->createNdJsonBatchesFromFile($dumpedFilename) as $uuid => $filenames) {
            $batches[$uuid] = $filenames;
        }

        /*
         * Indexing
         */
        // Create new index version
        $newVersion = $this->crmIndexer->createIndexVersion($orgaUuid);

        // Upload ndjson files
        $tasks = [];
        foreach ($batches[$orgaUuid] as $file) {
            $tasks[] = $this->crmIndexer->indexFile($orgaUuid, $newVersion, $file);
        }

        // Wait for indexing to finish
        if ($tasks) {
            $this->crmIndexer->waitForIndexing($tasks);
        }

        // Create organization members search keys and swap live index version
        $this->crmIndexer->bumpIndexVersion($orgaUuid, $newVersion);
    }
}
