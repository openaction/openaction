<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\PetitionCategory;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PetitionCategory>
 */
class PetitionCategoryRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetitionCategory::class);
    }

    /**
     * @return PetitionCategory[]|array
     */
    public function getProjectCategories(Project $project, $hydrationMode = Query::HYDRATE_OBJECT): iterable
    {
        $qb = $this->createQueryBuilder('pc')
            ->select('pc')
            ->where('pc.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('pc.weight')
        ;

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * @return int[]
     */
    public function countPetitionsByProjectCategory(Project $project): array
    {
        $data = $this->_em->getConnection()->executeQuery('
            SELECT c.id, COUNT(*)
            FROM website_petitions_localized_petitions_localized_categories plc
            LEFT JOIN website_petitions_localized_categories c ON plc.petition_category_id = c.id
            WHERE c.project_id = ?
            GROUP BY c.id
        ', [$project->getId()]);

        $counts = [];
        foreach ($data->fetchAllAssociative() as $row) {
            $counts[$row['id']] = $row['count'];
        }

        return $counts;
    }

    public function sort(array $data): void
    {
        $connection = $this->_em->getConnection();
        $tableName = $this->_class->getTableName();

        $connection->transactional(static function () use ($connection, $tableName, $data) {
            foreach ($data as $item) {
                if (!isset($item['order'], $item['id'])) {
                    throw new \InvalidArgumentException('Invalid params order');
                }

                $connection->update($tableName, ['weight' => (int) $item['order']], ['uuid' => $item['id']]);
            }
        });
    }

    public function updateCategoriesForLocalized(\App\Entity\Website\LocalizedPetition $localized, array $categoriesIds): void
    {
        $qb = $this->createQueryBuilder('c');

        if ($ids = array_filter($categoriesIds, fn ($id) => !\Symfony\Component\Uid\Uuid::isValid($id))) {
            $qb->orWhere('c.id IN (:ids)')->setParameter('ids', $ids);
        }

        if ($uuids = array_filter($categoriesIds, fn ($id) => \Symfony\Component\Uid\Uuid::isValid($id))) {
            $qb->orWhere('c.uuid IN (:uuids)')->setParameter('uuids', $uuids);
        }

        $categories = [];
        if ($ids || $uuids) {
            $categories = $qb->getQuery()->getResult();
        }

        $this->_em->wrapInTransaction(function () use ($localized, $categories) {
            $metadata = $this->_em->getClassMetadata(PetitionCategory::class);

            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['petitions']['joinTable']['name'])
                ->where('localized_petition_id = :lp')
                ->setParameter('lp', $localized->getId())
                ->execute()
            ;

            $localized->getCategories()->clear();
            foreach ($categories as $category) {
                $localized->getCategories()->add($category);
            }

            $this->_em->persist($localized);
            $this->_em->flush();
        });
    }
}
