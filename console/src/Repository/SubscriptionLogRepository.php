<?php

namespace App\Repository;

use App\Entity\SubscriptionLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubscriptionLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriptionLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriptionLog[]    findAll()
 * @method SubscriptionLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionLog::class);
    }
}
