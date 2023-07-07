<?php

namespace App\Repository;

use App\Entity\Domain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Domain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domain[]    findAll()
 * @method Domain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainRepository extends ServiceEntityRepository
{
    private string $trialsDomainName;

    public function __construct(ManagerRegistry $registry, string $trialsDomainName)
    {
        parent::__construct($registry, Domain::class);

        $this->trialsDomainName = $trialsDomainName;
    }

    public function getTrialDomain(): Domain
    {
        return $this->findOneBy(['name' => $this->trialsDomainName]);
    }

    /**
     * @return Domain[]
     */
    public function findDomainsWithoutStatus(string $status): iterable
    {
        $ids = array_column(
            $this->_em->getConnection()
                ->prepare('SELECT id FROM domains WHERE configuration_status::text NOT LIKE ? ORDER BY id')
                ->executeQuery(['%'.$status.'%'])
                ->fetchAllAssociative(),
            'id'
        );

        if (!$ids) {
            return [];
        }

        $qb = $this->createQueryBuilder('d');
        $qb->where($qb->expr()->in('d.id', $ids));

        return $qb->getQuery()->toIterable();
    }
}
