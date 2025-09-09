<?php

namespace App\Bridge\Meilisearch;

use App\Bridge\Meilisearch\Model\Task;
use MeiliSearch\Client;
use MeiliSearch\Contracts\IndexesQuery;
use MeiliSearch\Endpoints\Indexes;
use MeiliSearch\Endpoints\Keys;
use MeiliSearch\Exceptions\ApiException;

class Meilisearch implements MeilisearchInterface
{
    private string $endpoint;
    private string $masterKey;
    private string $namespace;

    private ?Client $client = null;

    public function __construct(string $endpoint, string $masterKey, string $namespace)
    {
        $this->endpoint = $endpoint;
        $this->masterKey = $masterKey;
        $this->namespace = $namespace;
    }

    public function indexExists(string $index): bool
    {
        try {
            $this->getClient()->index($this->prefixNamespace($index))->fetchRawInfo();

            return true;
        } catch (ApiException) {
            return false;
        }
    }

    public function listIndexes(): array
    {
        $indexes = $this->getClient()->getAllIndexes((new IndexesQuery())->setOffset(0)->setLimit(5000))->getResults();

        $names = [];

        /** @var Indexes $index */
        foreach ($indexes as $index) {
            $names[] = $index->getUid();
        }

        return $names;
    }

    public function createIndex(string $index, array $searchableAttributes, array $filterableAttributes, array $sortableAttributes): Task
    {
        $created = $this->getClient()->index($index);
        $created->updateSearchableAttributes($searchableAttributes);
        $created->updateFilterableAttributes($filterableAttributes);
        $created->updateSortableAttributes($sortableAttributes);
        $created->updateFaceting(['maxValuesPerFacet' => 1_000_000]);

        return $this->mapToTask($created->updatePagination(['maxTotalHits' => 999_000_000_000]));
    }

    public function deleteIndex(string $index): ?Task
    {
        try {
            return $this->mapToTask($this->getClient()->deleteIndex($index));
        } catch (\Throwable) {
            return null;
        }
    }

    public function indexDocumentsBatch(string $index, string $documents): ?Task
    {
        if (!$documents) {
            return null;
        }

        return $this->mapToTask($this->getClient()->index($index)->addDocumentsNdjson($documents));
    }

    public function unindexDocuments(string $index, array $documentsIds): ?Task
    {
        if (!$documentsIds) {
            return null;
        }

        return $this->mapToTask($this->getClient()->index($index)->deleteDocuments($documentsIds));
    }

    public function unindexAllDocuments(string $index): Task
    {
        return $this->mapToTask($this->getClient()->index($index)->deleteAllDocuments());
    }

    /**
     * @param Task[] $tasks
     */
    public function waitForTasks(array $tasks, int $timeoutInMs = 5000, int $intervalInMs = 50): void
    {
        $uids = array_map(static fn (Task $t) => $t->getUid(), $tasks);
        $this->getClient()->waitForTasks($uids, $timeoutInMs, $intervalInMs);
    }

    public function search(string $index, ?string $query = null, array $searchParams = [], array $options = []): array
    {
        return $this->getClient()->index($index)->search($query, $searchParams, [...$options, 'raw' => true]);
    }

    public function findFacetStats(string $index, array $facets, array $searchParams = [], array $options = []): array
    {
        $searchParams['facets'] = $facets;

        // Populate as little hits as possible as we are only interested in the stats
        $searchParams['attributesToRetrieve'] = ['id'];
        $searchParams['limit'] = 1;

        $response = $this->search($index, null, $searchParams, $options);

        $stats = $response['facetDistribution'] ?? [];
        $stats['total'] = $response['nbHits'];

        return $stats;
    }

    public function createApiKey(array $indexes, array $actions, ?string $description = null): Keys
    {
        return $this->getClient()->createKey([
            'indexes' => $indexes,
            'actions' => $actions,
            'description' => $description,
            'expiresAt' => null,
        ]);
    }

    public function deleteApiKey(string $key): void
    {
        try {
            $this->getClient()->deleteKey($key);
        } catch (\Throwable) {
            // no-op
        }
    }

    public function createTenantToken(string $parentKeyUid, string $parentKey, array $searchRules): string
    {
        return $this->getClient()->generateTenantToken($parentKeyUid, $searchRules, ['apiKey' => $parentKey]);
    }

    private function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client($this->endpoint, $this->masterKey);
        }

        return $this->client;
    }

    private function prefixNamespace(string $index): string
    {
        return $this->namespace.'_'.$index;
    }

    private function mapToTask(array $data): Task
    {
        return new Task($data['taskUid'], $data['status']);
    }
}
