<?php

namespace App\Search\Consumer;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Repository\OrganizationRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveCrmDocumentHandler implements MessageHandlerInterface
{
    public function __construct(private OrganizationRepository $repository, private MeilisearchInterface $meilisearch)
    {
    }

    public function __invoke(RemoveCrmDocumentMessage $message)
    {
        if ($orga = $this->repository->find($message->getOrganizationId())) {
            $this->meilisearch->waitForTasks([
                $this->meilisearch->unindexDocuments($orga->getCrmIndexName(), [$message->getContactUuid()]),
            ]);
        }
    }
}
