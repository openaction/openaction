<?php

namespace App\Repository\Community;

use App\Entity\Community\ContactMembership;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactMembership|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactMembership|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactMembership[]    findAll()
 * @method ContactMembership[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMembership::class);
    }
}
