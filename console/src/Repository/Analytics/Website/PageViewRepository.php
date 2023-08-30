<?php

namespace App\Repository\Analytics\Website;

use App\Entity\Analytics\Website\PageView;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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

    /**
     * @return PageView[]
     */
    public function findOldestSessionPageViewsFor(Project $project): array
    {
        $oldestPageView = $this->createQueryBuilder('v')
            ->select('v.date', 'v.hash')
            ->where('v.project = :project')
            ->setParameter('project', $project)
            ->andWhere('v.date <= :oneHourAgo')
            ->setParameter('oneHourAgo', new \DateTime('-1 hour'))
            ->orderBy('v.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY)
        ;

        if (!$oldestPageView) {
            return [];
        }

        $startDate = clone $oldestPageView['date'];
        $endDate = (clone $oldestPageView['date'])->modify('+30 minutes');

        /** @var Uuid $hash */
        $hash = $oldestPageView['hash'];

        $pageViews = $this->createQueryBuilder('v')
            ->select('v')
            ->where('v.project = :project')
            ->setParameter('project', $project)
            ->andWhere('v.date >= :startDate')
            ->setParameter('startDate', $startDate)
            ->andWhere('v.date < :endDate')
            ->setParameter('endDate', $endDate)
            ->andWhere('v.hash = :hash')
            ->setParameter('hash', $hash->toRfc4122())
            ->orderBy('v.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $result = [];
        foreach ($pageViews as $pageView) {
            $result[] = $pageView;
        }

        return $result;
    }

    /**
     * @param PageView[] $pageViews
     */
    public function removeOldestSessionPageViews(array $pageViews)
    {
        $ids = [];
        foreach ($pageViews as $pageView) {
            $ids[] = $pageView->getId();
        }

        if (!$ids) {
            return;
        }

        $qb = $this->createQueryBuilder('v');
        $qb->delete()->where($qb->expr()->in('v.id', $ids))->getQuery()->execute();
    }
}
