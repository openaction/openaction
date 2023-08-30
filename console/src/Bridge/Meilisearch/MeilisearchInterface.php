<?php

namespace App\Bridge\Meilisearch;

use App\Bridge\Meilisearch\Model\Task;
use MeiliSearch\Endpoints\Keys;

interface MeilisearchInterface
{
    public function indexExists(string $index): bool;

    public function createIndex(string $index, array $searchableAttributes, array $filterableAttributes, array $sortableAttributes): Task;

    public function deleteIndex(string $index): ?Task;

    public function indexDocumentsBatch(string $index, string $documents): Task;

    public function unindexDocuments(string $index, array $documentsIds): Task;

    public function unindexAllDocuments(string $index): Task;

    /**
     * @param Task[] $tasks
     */
    public function waitForTasks(array $tasks, int $timeoutInMs = 5000, int $intervalInMs = 50): void;

    public function createApiKey(array $indexes, array $actions, string $description = null): Keys;

    public function deleteApiKey(string $key): void;

    public function createTenantToken(string $parentKeyUid, string $parentKey, array $searchRules): string;

    public function search(string $index, ?string $query = null, array $searchParams = [], array $options = []): array;
}
