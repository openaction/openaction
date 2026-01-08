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
}
