<?php

namespace App\Repository\Website;

use App\Entity\Website\FormBlock;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormBlock|null findOneByBase62Uid(string $base62Uid)
 * @method FormBlock[]    findAll()
 * @method FormBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormBlockRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormBlock::class);
    }
}
