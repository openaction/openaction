<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrganizationMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizationMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizationMember[]    findAll()
 * @method OrganizationMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationMemberRepository extends ServiceEntityRepository
{
    private array $membersCache = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationMember::class);
    }

    public function findByOrganizationGrouped(Organization $organization): array
    {
        /** @var OrganizationMember[] $members */
        $members = $this->createQueryBuilder('m')
            ->select('m', 'u')
            ->leftJoin('m.member', 'u')
            ->where('m.organization = :organization')
            ->setParameter('organization', $organization->getId())
            ->getQuery()
            ->getResult()
        ;

        $list = ['administrators' => [], 'collaborators' => []];
        foreach ($members as $member) {
            $list[$member->isAdmin() ? 'administrators' : 'collaborators'][] = $member;
        }

        return $list;
    }

    public function findMember(User $user, Organization $organization): ?OrganizationMember
    {
        if (!isset($this->membersCache[$organization->getId()][$user->getId()])) {
            $this->membersCache[$organization->getId()][$user->getId()] = $this->createQueryBuilder('m')
                ->select('m')
                ->andWhere('m.member = :user')
                ->setParameter('user', $user)
                ->andWhere('m.organization = :organization')
                ->setParameter('organization', $organization)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }

        return $this->membersCache[$organization->getId()][$user->getId()];
    }

    public function findOneAdmin(Organization $organization): ?OrganizationMember
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->andWhere('m.isAdmin = TRUE')
            ->andWhere('m.organization = :organization')
            ->setParameter('organization', $organization)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
