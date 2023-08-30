<?php

namespace App\Repository\Billing;

use App\Entity\Billing\Order;
use App\Entity\Organization;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order|null findOneByBase62Uid(string $base62Uid)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return Order[]
     */
    public function findInvoicesHistory(Organization $orga): iterable
    {
        return $this->createQueryBuilder('i')
            ->where('i.invoiceNumber IS NOT NULL')
            ->andWhere('i.organization = :orga')
            ->setParameter('orga', $orga)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNextInvoiceNumber(): int
    {
        return 1 + (
            $this->createQueryBuilder('i')
                ->select('MAX(i.invoiceNumber)')
                ->getQuery()
                ->getSingleScalarResult() ?? 0
        );
    }

    /**
     * @return iterable|Order[]
     */
    public function findInvoicesToGenerate(): iterable
    {
        return $this
            ->createQueryBuilder('i')
            ->where('i.paidAt IS NOT NULL')
            ->andWhere('i.invoiceNumber IS NOT NULL')
            ->andWhere('i.invoicePdf IS NULL')

            // Avoid generating invoices for orders possibly in the queue
            ->andWhere('i.paidAt < :onehourago')
            ->setParameter('onehourago', new \DateTime('-1 hour'))

            ->getQuery()
            ->getResult()
        ;
    }
}
