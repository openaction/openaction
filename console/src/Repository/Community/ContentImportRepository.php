<?php

namespace App\Repository\Community;

use App\Entity\Community\ContentImport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContentImport|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentImport|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentImport[]    findAll()
 * @method ContentImport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentImportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentImport::class);
    }
}
