<?php

namespace App\Search\Consumer;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Repository\Platform\JobRepository;
use App\Search\Model\BatchRequest;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

abstract class AbstractCrmBatchHandler implements MessageHandlerInterface
{
    private MeilisearchInterface $meilisearch;
    private OrganizationRepository $organizationRepository;
    private JobRepository $jobRepository;

    protected function createBatchIterable(Organization $orga, BatchRequest $request, int $perPage, array $params = []): iterable
    {
        $page = 0;
        $maxPages = 1;

        while ($page < $maxPages) {
            ++$page;

            // Fetch page
            $data = $this->meilisearch->search($orga->getCrmIndexName(), $request->queryInput, array_merge($params, [
                'filter' => $request->queryFilter,
                'sort' => $request->querySort,
                'limit' => $perPage,
                'offset' => ($page - 1) * $perPage,
            ]));

            $maxPages = ceil($data['estimatedTotalHits'] / $perPage);

            foreach ($data['hits'] as $hit) {
                yield $hit;
            }
        }
    }

    public function getOrganizationRepository(): OrganizationRepository
    {
        return $this->organizationRepository;
    }

    protected function getJobRepository(): JobRepository
    {
        return $this->jobRepository;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setMeilisearch(MeilisearchInterface $meilisearch)
    {
        $this->meilisearch = $meilisearch;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setOrganizationRepository(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setJobRepository(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }
}
