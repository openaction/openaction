<?php

namespace App\Repository\Website;

use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Website\Page;
use App\Entity\Website\PageCategory;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method PageCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageCategory|null findOneByBase62Uid(string $base62Uid)
 * @method PageCategory[]    findAll()
 * @method PageCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageCategoryRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageCategory::class);
    }

    /**
     * @return PageCategory[]
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
     * @return PageCategory[]
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

    public function updateCategories(Page $page, array $categoriesIds)
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

        $this->_em->wrapInTransaction(function () use ($page, $categories) {
            $metadata = $this->_em->getClassMetadata(PageCategory::class);

            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['pages']['joinTable']['name'])
                ->where('page_id = :page')
                ->setParameter('page', $page->getId())
                ->execute()
            ;

            $page->getCategories()->clear();
            foreach ($categories as $category) {
                $page->getCategories()->add($category);
            }

            $this->_em->persist($page);
            $this->_em->flush();
        });
    }
}
