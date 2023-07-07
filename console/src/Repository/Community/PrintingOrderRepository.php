<?php

namespace App\Repository\Community;

use App\Entity\Community\PrintingOrder;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrintingOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintingOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintingOrder[]    findAll()
 * @method PrintingOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintingOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintingOrder::class);
    }

    /**
     * @return PrintingOrder[]
     */
    public function findAllDrafts(Project $project)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('c.order IS NULL')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllOrderedPaginator(Project $project, int $currentPage, int $limit = 30): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder('o')
                ->select('o', 'bo')
                ->leftJoin('o.order', 'bo')
                ->where('o.project = :project')
                ->setParameter('project', $project->getId())
                ->andWhere('o.order IS NOT NULL')
                ->orderBy('bo.createdAt', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult(($currentPage - 1) * $limit)
                ->getQuery()
        );
    }

    public function countByOrderedStatus(bool $isOrdered): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->where('o.order '.($isOrdered ? 'IS NOT NULL' : 'IS NULL'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countWaitingForPayment(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->leftJoin('o.order', 'bo')
            ->where('o.order IS NOT NULL')
            ->where('bo.paidAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
