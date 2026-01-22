<?php

namespace App\Repository\Community;

use App\Entity\Community\EmailBatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailBatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailBatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailBatch[]    findAll()
 * @method EmailBatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailBatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailBatch::class);
    }

    public function markSent(EmailBatch $batch): void
    {
        $this->createQueryBuilder('b')
            ->update()
            ->set('b.sentAt', ':now')
            ->setParameter('now', new \DateTime())
            ->where('b.id = :id')
            ->setParameter('id', $batch->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @return EmailBatch[]
     */
    public function findDueCampaignBatches(\DateTimeInterface $now, int $limit = 500): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.scheduledAt IS NOT NULL')
            ->andWhere('b.scheduledAt <= :now')
            ->andWhere('b.sentAt IS NULL')
            ->andWhere('b.queuedAt IS NULL')
            ->andWhere('b.source LIKE :source')
            ->setParameter('now', $now)
            ->setParameter('source', 'campaign:%')
            ->orderBy('b.scheduledAt', 'ASC')
            ->addOrderBy('b.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function markQueued(EmailBatch $batch, ?\DateTimeInterface $queuedAt = null): bool
    {
        $queuedAt ??= new \DateTime();

        $updated = $this->createQueryBuilder('b')
            ->update()
            ->set('b.queuedAt', ':queuedAt')
            ->setParameter('queuedAt', $queuedAt)
            ->where('b.id = :id')
            ->andWhere('b.queuedAt IS NULL')
            ->andWhere('b.sentAt IS NULL')
            ->setParameter('id', $batch->getId())
            ->getQuery()
            ->execute();

        return $updated > 0;
    }
}
