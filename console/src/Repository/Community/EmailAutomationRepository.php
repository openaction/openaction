<?php

namespace App\Repository\Community;

use App\Entity\Community\EmailAutomation;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailAutomation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailAutomation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailAutomation[]    findAll()
 * @method EmailAutomation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailAutomationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailAutomation::class);
    }

    /**
     * @return EmailAutomation[]
     */
    public function findAllFor(Organization $organization, bool $enabled): iterable
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.enabled = :enabled')
            ->setParameter('enabled', $enabled)
            ->andWhere('a.organization = :orga')
            ->setParameter('orga', $organization)
            ->orderBy('a.enabled', 'DESC')
            ->addOrderBy('a.weight', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
