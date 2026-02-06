<?php

namespace App\Repository\Analytics\Website;

use App\Entity\Analytics\Website\PageView;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageView|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageView|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageView[]    findAll()
 * @method PageView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageView::class);
    }

    public function countLiveVisitors(Project $project): int
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(DISTINCT v.hash)')
            ->where('v.project = :project')
            ->setParameter('project', $project)
            ->andWhere('v.date >= :tenminago')
            ->setParameter('tenminago', new \DateTime('10 minutes ago'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countAllLiveVisitors(): int
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(DISTINCT v.hash)')
            ->andWhere('v.date >= :tenminago')
            ->setParameter('tenminago', new \DateTime('10 minutes ago'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
