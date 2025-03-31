<?php

namespace App\Search\Consumer;

use App\Repository\Community\ContactRepository;
use App\Search\CrmIndexer;
use App\Search\Model\BatchRequest;

final class RemoveCrmBatchHandler extends AbstractCrmBatchHandler
{
    public function __construct(
        private ContactRepository $contactRepository,
        private CrmIndexer $crmIndexer,
    ) {
    }

    public function __invoke(RemoveCrmBatchMessage $message)
    {
        // Reset job (for ex if message is requeued)
        $this->getJobRepository()->resetJob($message->getJobId());

        // Resolve batch
        if (!$orga = $this->getOrganizationRepository()->find($message->getOrganizationId())) {
            return true;
        }

        $orgaUuid = $orga->getUuid()->toRfc4122();
        $orgaIndexVersion = $orga->getCrmIndexVersion();

        $batch = $this->createBatchIterable($orga, BatchRequest::createFromPayload($message->getBatchRequest()), 50_000, [
            'attributesToRetrieve' => ['id'], // Only retrieve ID to improve search performance
        ]);

        // Resolve refresh rate depending on job size
        $totalSteps = $this->getJobRepository()->getJobTotalSteps($message->getJobId());

        // Minimum batch size of 50 for performance
        $batchSize = max(ceil($totalSteps / 100), 50);

        // Process
        $steps = 0;
        $toUpdate = [];

        foreach ($batch as $document) {
            ++$steps;
            $toUpdate[] = $document['id'];

            if (0 === $steps % $batchSize) {
                // Update database
                $this->contactRepository->removeBatch($toUpdate);

                // Refresh CRM
                $this->crmIndexer->waitForIndexing(array_filter([
                    $this->crmIndexer->removeContacts($orgaUuid, $orgaIndexVersion, $toUpdate),
                ]));

                // Advance job
                $this->getJobRepository()->advanceJobStep($message->getJobId(), $batchSize);

                // Start new batch
                $toUpdate = [];
            }
        }

        // Apply last batch
        if ($toUpdate) {
            // Update database
            $this->contactRepository->removeBatch($toUpdate);

            // Refresh CRM
            $this->crmIndexer->waitForIndexing(array_filter([
                $this->crmIndexer->removeContacts($orgaUuid, $orgaIndexVersion, $toUpdate),
            ]));
        }

        // Finish job
        $this->getJobRepository()->finishJob($message->getJobId());

        return true;
    }
}
