<?php

namespace App\Repository\Community;

use App\Entity\Community\ContactPayment;
use App\Entity\Community\ContactSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContactPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactPayment::class);
    }

    public function createOrganizationPaymentsPaginator(
        \App\Entity\Organization $organization,
        array $filters,
        int $page,
        int $perPage,
    ): \Doctrine\ORM\Tools\Pagination\Paginator {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.contact', 'c')
            ->andWhere('c.organization = :orga')
            ->setParameter('orga', $organization)
            ->orderBy('p.createdAt', 'DESC')
        ;

        if (!empty($filters['type'])) {
            $qb->andWhere('p.type = :type')->setParameter('type', $filters['type']);
        }
        if (!empty($filters['method'])) {
            $qb->andWhere('p.paymentMethod = :method')->setParameter('method', $filters['method']);
        }
        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'captured':
                    $qb->andWhere('p.capturedAt IS NOT NULL');
                    break;
                case 'failed':
                    $qb->andWhere('p.failedAt IS NOT NULL');
                    break;
                case 'refunded':
                    $qb->andWhere('p.refundedAt IS NOT NULL');
                    break;
                case 'canceled':
                    $qb->andWhere('p.canceledAt IS NOT NULL');
                    break;
                case 'pending':
                    $qb->andWhere('p.capturedAt IS NULL')
                        ->andWhere('p.failedAt IS NULL')
                        ->andWhere('p.refundedAt IS NULL')
                        ->andWhere('p.canceledAt IS NULL');
                    break;
            }
        }

        if (isset($filters['amount_min']) && null !== $filters['amount_min'] && '' !== $filters['amount_min']) {
            $qb->andWhere('(p.netAmount + p.feesAmount) >= :minTotal')
               ->setParameter('minTotal', (int) $filters['amount_min']);
        }
        if (isset($filters['amount_max']) && null !== $filters['amount_max'] && '' !== $filters['amount_max']) {
            $qb->andWhere('(p.netAmount + p.feesAmount) <= :maxTotal')
               ->setParameter('maxTotal', (int) $filters['amount_max']);
        }

        if (!empty($filters['date_min'])) {
            $qb->andWhere('p.createdAt >= :dateMin')->setParameter('dateMin', new \DateTime($filters['date_min']));
        }
        if (!empty($filters['date_max'])) {
            $qb->andWhere('p.createdAt <= :dateMax')->setParameter('dateMax', new \DateTime($filters['date_max']));
        }

        $qb->setFirstResult(max(0, $page - 1) * $perPage)->setMaxResults($perPage);

        return new \Doctrine\ORM\Tools\Pagination\Paginator($qb);
    }

    public function findLatestForSubscription(ContactSubscription $subscription): ?ContactPayment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.subscription = :subscription')
            ->setParameter('subscription', $subscription)
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsForSubscriptionAndDate(ContactSubscription $subscription, \DateTimeImmutable $date): bool
    {
        $startOfDay = $date->setTime(0, 0, 0);
        $endOfDay = $startOfDay->modify('+1 day');

        $count = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.subscription = :subscription')
            ->setParameter('subscription', $subscription)
            ->andWhere('p.createdAt >= :startOfDay')
            ->setParameter('startOfDay', \DateTime::createFromImmutable($startOfDay))
            ->andWhere('p.createdAt < :endOfDay')
            ->setParameter('endOfDay', \DateTime::createFromImmutable($endOfDay))
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }
}
