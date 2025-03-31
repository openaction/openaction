<?php

namespace App\Search\Crm;

use App\Bridge\Meilisearch\MeilisearchInterface;
use App\Bridge\Meilisearch\Model\Task;
use App\Repository\OrganizationRepository;
use App\Search\CrmIndexer;
use App\Search\TenantTokenManager;
use Doctrine\ORM\EntityManagerInterface;

class CrmDataIndexer
{
    public function __construct(
        private MeilisearchInterface $meilisearch,
        private OrganizationRepository $repository,
        private EntityManagerInterface $em,
        private TenantTokenManager $tenantTokenManager,
    ) {
    }

    public function createIndexVersion(string $organizationUuid): string
    {
        $version = date('Y-m-d-H-i-s').'-'.substr(md5(uniqid('', true)), 0, 6);

        $task = $this->meilisearch->createIndex(
            CrmIndexer::getIndexName($organizationUuid, $version),
            CrmIndexer::SEARCHABLE_ATTRIBUTES,
            CrmIndexer::FILTERABLE_ATTRIBUTES,
            CrmIndexer::SORTABLE_ATTRIBUTES,
        );

        $this->meilisearch->waitForTasks([$task], 60_000, 2_000);

        return $version;
    }

    public function indexFile(string $organizationUuid, string $version, string $filename): ?Task
    {
        return $this->meilisearch->indexDocumentsBatch(
            CrmIndexer::getIndexName($organizationUuid, $version),
            trim(file_get_contents($filename)),
        );
    }

    public function unindexDocuments(string $organizationUuid, string $version, array $documentsIds): ?Task
    {
        return $this->meilisearch->unindexDocuments(CrmIndexer::getIndexName($organizationUuid, $version), $documentsIds);
    }

    public function waitForIndexing(array $tasks): void
    {
        $this->meilisearch->waitForTasks($tasks, 3_600_000, 2_000);
    }

    public function bumpIndexVersion(string $organizationUuid, string $newVersion): void
    {
        if (!$orga = $this->repository->findOneByUuid($organizationUuid)) {
            throw new \InvalidArgumentException('Organization with UUID '.$organizationUuid.' not found');
        }

        $newIndex = CrmIndexer::getIndexName($organizationUuid, $newVersion);

        // Create search key
        $searchKey = $this->meilisearch->createApiKey([$newIndex], ['search', 'documents.get', 'stats.get']);

        $oldSearchKey = $orga->getCrmSearchKey();
        $orga->setCrmSearchKey($searchKey->getKey());
        $orga->setCrmSearchKeyUid($searchKey->getUid());

        // Swap index version
        $oldVersion = $orga->getCrmIndexVersion();
        $orga->setCrmIndexVersion($newVersion);

        $this->em->persist($orga);

        // Create members tenant tokens
        foreach ($orga->getMembers() as $member) {
            $this->tenantTokenManager->refreshMemberCrmTenantToken($member, persist: false);
            $this->em->persist($member);
        }

        // Apply changes all at once to avoid accessing indexes with wrong key/tokens
        $this->em->flush();

        // Clear old key and index
        if ($oldSearchKey) {
            $this->meilisearch->deleteApiKey($oldSearchKey);
        }

        if ($oldVersion) {
            $this->meilisearch->deleteIndex(CrmIndexer::getIndexName($organizationUuid, $oldVersion));
        }
    }
}
