<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organization[]    findAll()
 * @method Organization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organization::class);
    }

    public function findAllUuids(): array
    {
        return array_column(
            $this->createQueryBuilder('o')->select('o.uuid')->getQuery()->getArrayResult(),
            'uuid',
        );
    }

    public function countActiveSubscriptions(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->where('o.subscriptionTrialing = FALSE')
            ->andWhere('o.billingPricePerMonth > 0')
            ->andWhere('o.subscriptionCurrentPeriodEnd > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countTrialingSubscriptions(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->where('o.subscriptionTrialing = TRUE')
            ->andWhere('o.subscriptionCurrentPeriodEnd > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countExpiredSubscriptions(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere('o.subscriptionCurrentPeriodEnd <= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return Organization[]
     */
    public function findActiveSubscriptions(): iterable
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.subscriptionTrialing = FALSE')
            ->andWhere('o.billingPricePerMonth > 0')
            ->andWhere('o.subscriptionCurrentPeriodEnd > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Organization[]
     */
    public function findAlmostExpiredPayingSubscriptions(): iterable
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.subscriptionTrialing = FALSE')
            ->andWhere('o.billingPricePerMonth > 0')
            ->andWhere('o.subscriptionCurrentPeriodEnd > :now')
            ->setParameter('now', new \DateTime())
            ->andWhere('o.subscriptionCurrentPeriodEnd < :in30days')
            ->setParameter('in30days', new \DateTime('+30 days'))
            ->orderBy('o.subscriptionCurrentPeriodEnd', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByUuid(string $uuid): ?Organization
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByPartner(User $partner): iterable
    {
        if ($partner->isAdmin()) {
            return $this->findBy([], ['name' => 'ASC']);
        }

        return $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.partner = :partner')
            ->setParameter('partner', $partner)
            ->orderBy('o.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findUserSwitcher(User $user): array
    {
        return $this->_em->createQueryBuilder()
            ->select('DISTINCT o.uuid', 'o.name')
            ->from(OrganizationMember::class, 'm')
            ->leftJoin('m.organization', 'o')
            ->where('m.member = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('o.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function createMoveQueryBuilder(User $user, Organization $current): QueryBuilder
    {
        $adminOrgasIds = [0];
        foreach ($user->getMemberships() as $membership) {
            if ($membership->isAdmin()) {
                $adminOrgasIds[] = $membership->getOrganization()->getId();
            }
        }

        $projectsCountSubquery = $this->_em->createQueryBuilder()
            ->select('COUNT(sp)')
            ->from(Project::class, 'sp')
            ->where('sp.organization = o.id')
            ->getDQL()
        ;

        $qb = $this->createQueryBuilder('o');

        $allowedIds = (array) $qb
            ->select('o.id')
            ->where($qb->expr()->in('o.id', $adminOrgasIds))
            ->andWhere('o.id != :currentOrganization')
            ->having('('.$projectsCountSubquery.') < o.projectsSlots')
            ->setParameter('currentOrganization', $current->getId())
            ->groupBy('o.id')
            ->getQuery()
            ->getSingleColumnResult()
        ;

        // Ensure the query won't fail even without results
        $allowedIds[] = 0;

        $qb = $this->createQueryBuilder('o');

        return $qb->select('o')->where($qb->expr()->in('o.id', $allowedIds));
    }

    public function useCredits(Organization $organization, int $amount, string $action): bool
    {
        if ($organization->getCreditsBalance() < $amount) {
            return false;
        }

        $organization->useCredits($amount, $action);

        $this->_em->persist($organization);
        $this->_em->flush();

        return true;
    }

    public function useTextsCredits(Organization $organization, int $amount, string $action): bool
    {
        if ($organization->getTextsCreditsBalance() < $amount) {
            return false;
        }

        $organization->useTextsCredits($amount, $action);

        $this->_em->persist($organization);
        $this->_em->flush();

        return true;
    }
}
