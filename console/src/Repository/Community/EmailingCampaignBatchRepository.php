<?php

namespace App\Repository\Community;

use App\Entity\Community\EmailingCampaignBatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailingCampaignBatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailingCampaignBatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailingCampaignBatch[]    findAll()
 * @method EmailingCampaignBatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailingCampaignBatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailingCampaignBatch::class);
    }

    public function markSent(EmailingCampaignBatch $batch): void
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
