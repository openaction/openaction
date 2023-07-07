<?php

namespace App\Search;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Repository\Website\EventRepository;
use App\Repository\Website\PageRepository;
use App\Repository\Website\PostRepository;
use App\Search\Model\Searchable;
use App\Util\Json;

class CmsIndexer
{
    public const INDEX_NAME = 'public';

    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PostRepository $postRepository,
        private readonly EventRepository $eventRepository,
        private readonly Document\SearchDocumentFactory $documentFactory,
        private readonly MeilisearchInterface $meilisearch,
    ) {
    }

    public function indexDocument(Searchable $entity): void
    {
        $this->meilisearch->indexDocumentsBatch(
            self::INDEX_NAME,
            Json::encode($this->documentFactory->createDocument($entity)),
        );
    }

    public function unindexDocument(string $documentId): void
    {
        $this->meilisearch->unindexDocuments(self::INDEX_NAME, [$documentId]);
    }

    public function createPagesDocuments(): iterable
    {
        foreach ($this->pageRepository->getAllPublicPages() as $page) {
            yield $this->documentFactory->createDocument($page);
        }
    }

    public function createPostsDocuments(): iterable
    {
        foreach ($this->postRepository->getAllPublicPosts() as $post) {
            yield $this->documentFactory->createDocument($post);
        }
    }

    public function createEventsDocuments(): iterable
    {
        foreach ($this->eventRepository->getAllPublicEvents() as $event) {
            yield $this->documentFactory->createDocument($event);
        }
    }

    public function clearIndex(): void
    {
        $task = $this->meilisearch->unindexAllDocuments(self::INDEX_NAME);
        $this->meilisearch->waitForTasks([$task], 30_000, 500);
    }

    public function createIndex(): void
    {
        $task = $this->meilisearch->createIndex(
            self::INDEX_NAME,
            ['encoded_uuid', 'title', 'content'],
            ['type', 'restrictions_organization', 'restrictions_projects', 'date', 'areas', 'categories', 'status'],
            ['date', 'title', 'status'],
        );

        $this->meilisearch->waitForTasks([$task], 30_000, 500);
    }

    public function indexDocuments(array $documents): void
    {
        $tasks = [];
        foreach (array_chunk($documents, 1500) as $batch) {
            $tasks[] = $this->meilisearch->indexDocumentsBatch(self::INDEX_NAME, implode("\n", $batch));
        }

        $this->meilisearch->waitForTasks($tasks, 60_000, 2_000);
    }
}
