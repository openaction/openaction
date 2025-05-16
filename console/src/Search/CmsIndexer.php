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

    public function getAllDocumentsIds(): array
    {
        $data = $this->meilisearch->search(self::INDEX_NAME, '', [
            'limit' => 999_999_999,
            'attributesToRetrieve' => ['id'],
        ]);

        $ids = [];
        foreach ($data['hits'] as $row) {
            $ids[] = $row['id'];
        }

        return $ids;
    }

    public function configureIndex(): void
    {
        if (!$this->meilisearch->indexExists(self::INDEX_NAME)) {
            $task = $this->meilisearch->createIndex(
                self::INDEX_NAME,
                ['encoded_uuid', 'title', 'content'],
                ['type', 'restrictions_organization', 'restrictions_projects', 'date', 'areas', 'categories', 'status'],
                ['date', 'title', 'status'],
            );

            $this->meilisearch->waitForTasks([$task], 30_000, 500);
        }
    }

    public function indexDocuments(array $documents): void
    {
        foreach (array_chunk($documents, 1500) as $batch) {
            $this->meilisearch->indexDocumentsBatch(self::INDEX_NAME, implode("\n", $batch));
        }
    }

    public function unindexDocuments(array $documentsIds): void
    {
        foreach (array_chunk($documentsIds, 1500) as $batch) {
            $this->meilisearch->unindexDocuments(self::INDEX_NAME, $batch);
        }
    }
}
