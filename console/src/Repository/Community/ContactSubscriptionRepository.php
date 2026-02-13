<?php

namespace App\Repository\Community;

use App\Entity\Community\Contact;
use App\Entity\Community\ContactSubscription;
use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContactSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactSubscription::class);
    }

    public function findActiveByContactTypeMethod(
        Contact $contact,
        ContactPaymentType $type,
        ContactPaymentMethod $method,
    ): ?ContactSubscription {
        return $this->createQueryBuilder('s')
            ->andWhere('s.contact = :contact')
            ->setParameter('contact', $contact)
            ->andWhere('s.type = :type')
            ->setParameter('type', $type)
            ->andWhere('s.paymentMethod = :method')
            ->setParameter('method', $method)
            ->andWhere('s.active = true')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return ContactSubscription[]
     */
    public function findActiveNonExpired(\DateTimeImmutable $today, int $limit = 500): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.active = true')
            ->andWhere('s.endsAt IS NULL OR s.endsAt >= :today')
            ->setParameter('today', $today)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();
    }
}
