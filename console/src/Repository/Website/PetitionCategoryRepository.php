<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\PetitionCategory;
use App\Entity\Website\PetitionLocalized;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PetitionCategory>
 *
 * @method PetitionCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetitionCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetitionCategory[]    findAll()
 * @method PetitionCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetitionCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetitionCategory::class);
    }

    public function save(PetitionCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PetitionCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return PetitionCategory[]|array
     */
    public function getPetitionCategoriesForProject(Project $project, $hydrationMode = AbstractQuery::HYDRATE_OBJECT): iterable
    {
        $qb = $this->createQueryBuilder('pc')
            ->select('pc')
            ->where('pc.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('pc.weight');

        return $qb->getQuery()->getResult($hydrationMode);
    }

    public function countPetitionsByCategoriesForProject(Project $project): array
    {
        $data = $this->_em->getConnection()->executeQuery('
            SELECT c.id, COUNT(*)
            FROM website_petitions_localized_petitions_localized_categories pc
            LEFT JOIN website_petitions_localized_categories c ON pc.petition_category_id = c.id
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

    public function updateCategories(PetitionLocalized $petitionLocalized, array $categoriesIds): void
    {
        $this->_em->wrapInTransaction(function () use ($petitionLocalized, $categoriesIds) {
            $metadata = $this->_em->getClassMetadata(PetitionCategory::class);
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['petitionsLocalized']['joinTable']['name'])
                ->where('petition_localized_id = :pl_id')
                ->setParameter('pl_id', $petitionLocalized->getId())
                ->executeQuery()
            ;

            $petitionLocalized->getCategories()->clear();
            foreach ($categoriesIds as $id) {
                $petitionLocalized->getCategories()->add($this->find($id));
            }

            $this->_em->persist($petitionLocalized);
            $this->_em->flush();
        });
    }
}
