<?php

namespace App\Repository\Integration;

use App\Entity\Integration\RevueAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RevueAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method RevueAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method RevueAccount[]    findAll()
 * @method RevueAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevueAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevueAccount::class);
    }

    /**
     * @return \Generator|RevueAccount[]
     */
    public function findToSync(?string $orgaUuid = null): \Generator
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a', 'o')
            ->leftJoin('a.organization', 'o')
            ->orderBy('a.id')
        ;

        if ($orgaUuid) {
            $qb->where('o.uuid = :orgaUuid')->setParameter('orgaUuid', $orgaUuid);
        }

        foreach ($qb->getQuery()->toIterable() as $account) {
            yield $account;
        }
    }
}
