<?php

namespace App\Repository\Billing;

use App\Entity\Billing\Quote;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quote|null findOneByBase62Uid(string $base62Uid)
 * @method Quote[]    findAll()
 * @method Quote[]    findBy(array $criteria, array $QuoteBy = null, $limit = null, $offset = null)
 */
class QuoteRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }

    public function findNextQuoteNumber(): int
    {
        return 1 + (
            $this->createQueryBuilder('q')
                ->select('MAX(q.number)')
                ->getQuery()
                ->getSingleScalarResult() ?? 0
        );
    }
}
