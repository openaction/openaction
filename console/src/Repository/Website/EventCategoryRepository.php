<?php

namespace App\Repository\Website;

use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Website\Event;
use App\Entity\Website\EventCategory;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\Json;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method EventCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventCategory|null findOneByBase62Uid(string $base62Uid)
 * @method EventCategory[]    findAll()
 * @method EventCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventCategoryRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventCategory::class);
    }

    public function getUsedCategoriesJson(Project $project)
    {
        $categories = [];

        /** @var EventCategory[] $usedCategories */
        $usedCategories = $this->createQueryBuilder('c')
            ->where('c.project = :project')
            ->andWhere('c.events is not empty')
            ->orderBy('c.weight')
            ->setParameter('project', $project->getId())
            ->getQuery()
            ->getResult();

        foreach ($usedCategories as $id => $category) {
            $categories[$id]['id'] = $category->getId();
            $categories[$id]['name'] = $category->getName();
        }

        return Json::encode($categories);
    }

    /**
     * @return EventCategory[]
     */
    public function getProjectCategories(Project $project, $hydrationMode = Query::HYDRATE_OBJECT): iterable
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('ec', 'p')
            ->where('ec.project = :project')
            ->leftJoin('ec.events', 'p')
            ->setParameter('project', $project->getId())
            ->orderBy('ec.weight')
        ;

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * @return EventCategory[]
     */
    public function getOrganizationCategories(Organization $organization): array
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('ec', 'p')
            ->leftJoin('ec.project', 'p')
            ->where('p.organization = :organization')
            ->setParameter('organization', $organization->getId())
            ->orderBy('ec.weight')
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

    public function updateCategories(Event $event, array $categoriesIds)
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

        $this->_em->wrapInTransaction(function () use ($event, $categories) {
            $metadata = $this->_em->getClassMetadata(EventCategory::class);

            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['events']['joinTable']['name'])
                ->where('event_id = :event')
                ->setParameter('event', $event->getId())
                ->execute()
            ;

            $event->getCategories()->clear();
            foreach ($categories as $category) {
                $event->getCategories()->add($category);
            }

            $this->_em->persist($event);
            $this->_em->flush();
        });
    }
}
