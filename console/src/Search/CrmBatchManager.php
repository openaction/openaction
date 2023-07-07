<?php

namespace App\Search;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Entity\Organization;
use App\Entity\Platform\Job;
use App\Repository\Community\TagRepository;
use App\Repository\Platform\JobRepository;
use App\Search\Consumer\AddTagCrmBatchMessage;
use App\Search\Consumer\ExportCrmBatchMessage;
use App\Search\Consumer\RemoveCrmBatchMessage;
use App\Search\Consumer\RemoveTagCrmBatchMessage;
use App\Search\Model\BatchRequest;
use App\Util\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CrmBatchManager
{
    public function __construct(
        private ValidatorInterface $validator,
        private JobRepository $jobRepository,
        private MessageBusInterface $bus,
        private MeilisearchInterface $meilisearch,
        private TagRepository $tagRepository,
    ) {
    }

    public function parseBatchRequest(Request $request): BatchRequest
    {
        $batchRequest = BatchRequest::createFromPayload(Json::decode($request->getContent()));

        $errors = $this->validator->validate($batchRequest);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException();
        }

        return $batchRequest;
    }

    /**
     * Trigger job to add a tag on contacts of a given organization using given search filters.
     */
    public function addTagBatch(Organization $orga, BatchRequest $request): ?Job
    {
        if (!$tag = $this->tagRepository->find($request->params['tagId'] ?? null)) {
            return null;
        }

        if ($tag->getOrganization()->getId() !== $orga->getId()) {
            return null;
        }

        // Initialize job (and add 1 to the total steps for indexing)
        $job = $this->jobRepository->startJob('add_tag_batch', 0, $this->countMatches($orga, $request) + 1);

        // Start processing
        $this->bus->dispatch(new AddTagCrmBatchMessage($job->getId(), $orga->getId(), $request->toArray(), $tag->getId()));

        return $job;
    }

    /**
     * Trigger job to remove a tag from contacts of a given organization using given search filters.
     */
    public function removeTagBatch(Organization $orga, BatchRequest $request): ?Job
    {
        if (!$tag = $this->tagRepository->find($request->params['tagId'] ?? null)) {
            return null;
        }

        if ($tag->getOrganization()->getId() !== $orga->getId()) {
            return null;
        }

        // Initialize job (and add 1 to the total steps for indexing)
        $job = $this->jobRepository->startJob('remove_tag_batch', 0, $this->countMatches($orga, $request) + 1);

        // Start processing
        $this->bus->dispatch(new RemoveTagCrmBatchMessage($job->getId(), $orga->getId(), $request->toArray(), $tag->getId()));

        return $job;
    }

    /**
     * Trigger job to export contacts of a given organization using given search filters.
     */
    public function exportBatch(Organization $orga, BatchRequest $request): ?Job
    {
        // Initialize job
        $job = $this->jobRepository->startJob('export_batch', 0, $this->countMatches($orga, $request));

        // Start processing
        $this->bus->dispatch(new ExportCrmBatchMessage($job->getId(), $orga->getId(), $request->toArray()));

        return $job;
    }

    /**
     * Trigger job to remove contacts of a given organization using given search filters.
     */
    public function removeBatch(Organization $orga, BatchRequest $request): ?Job
    {
        // Initialize job
        $job = $this->jobRepository->startJob('remove_batch', 0, $this->countMatches($orga, $request));

        // Start processing
        $this->bus->dispatch(new RemoveCrmBatchMessage($job->getId(), $orga->getId(), $request->toArray()));

        return $job;
    }

    protected function countMatches(Organization $orga, BatchRequest $request): int
    {
        $data = $this->meilisearch->search($orga->getCrmIndexName(), $request->queryInput, [
            'filter' => $request->queryFilter,
            'sort' => $request->querySort,
            'limit' => 1,
            'attributesToRetrieve' => ['id'],
        ]);

        return $data['estimatedTotalHits'];
    }
}
