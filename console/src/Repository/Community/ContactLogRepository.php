<?php

namespace App\Repository\Community;

use App\Entity\Community\ContactLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactLog|null findOneByBase62Uid(string $base62Uid)
 * @method ContactLog[]    findAll()
 * @method ContactLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactLog::class);
    }
}
