<?php

namespace App\Repository\Website;

use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Website\TrombinoscopeCategory;
use App\Entity\Website\TrombinoscopePerson;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method TrombinoscopeCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrombinoscopeCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrombinoscopeCategory|null findOneByBase62Uid(string $base62Uid)
 * @method TrombinoscopeCategory[]    findAll()
 * @method TrombinoscopeCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrombinoscopeCategoryRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrombinoscopeCategory::class);
    }

    public function findProjectCategory(Project $project, int $id): ?TrombinoscopeCategory
    {
        return $this->findOneBy(['project' => $project, 'id' => $id]);
    }

    /**
     * @return TrombinoscopeCategory[]
     */
    public function getProjectCategories(Project $project, $hydrationMode = Query::HYDRATE_OBJECT): iterable
    {
        $qb = $this->createQueryBuilder('pc')
            ->select('pc', 'p')
            ->where('pc.project = :project')
            ->leftJoin('pc.persons', 'p')
            ->setParameter('project', $project->getId())
            ->orderBy('pc.weight')
        ;

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * @return TrombinoscopeCategory[]
     */
    public function getOrganizationCategories(Organization $organization): array
    {
        $qb = $this->createQueryBuilder('pc')
            ->select('pc', 'p')
            ->leftJoin('pc.project', 'p')
            ->where('p.organization = :organization')
            ->setParameter('organization', $organization->getId())
            ->orderBy('pc.weight')
        ;

        return $qb->getQuery()->getResult();
    }

    public function sort(array $data)
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

    public function updateCategories(TrombinoscopePerson $person, array $categoriesIds)
    {
        $qb = $this->createQueryBuilder('c');

        if ($ids = array_filter($categoriesIds, fn ($id) => !Uuid::isValid($id))) {
            $qb->orWhere('c.id IN (:ids)')->setParameter('ids', $ids);
        }

        if ($uuids = array_filter($categoriesIds, fn ($id) => Uuid::isValid($id))) {
            $qb->orWhere('c.uuid IN (:uuids)')->setParameter('uuids', $uuids);
        }

        $categories = [];
        if ($ids || $uuids) {
            $categories = $qb->getQuery()->getResult();
        }

        $this->_em->wrapInTransaction(function () use ($person, $categories) {
            $metadata = $this->_em->getClassMetadata(TrombinoscopeCategory::class);

            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['persons']['joinTable']['name'])
                ->where('trombinoscope_person_id = :person')
                ->setParameter('person', $person->getId())
                ->execute()
            ;

            $person->getCategories()->clear();
            foreach ($categories as $category) {
                $person->getCategories()->add($category);
            }

            $this->_em->persist($person);
            $this->_em->flush();
        });
    }
}
