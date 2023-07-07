<?php

namespace App\Repository\Community;

use App\Entity\Community\PrintingCampaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrintingCampaign|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintingCampaign|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintingCampaign[]    findAll()
 * @method PrintingCampaign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintingCampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintingCampaign::class);
    }

    /**
     * @return PrintingCampaign[]
     */
    public function createAdminToPrintExport(): iterable
    {
        return $this->createAdminExportQueryBuilder()
            ->where('(WORKFLOW_IS_IN_STEP(o.status, \'bat_validated\') = true AND WORKFLOW_IS_IN_STEP(o.status, \'payment_received\') = true)')
            ->orWhere('(WORKFLOW_IS_IN_STEP(o.status, \'printing\') = true)')
            ->orWhere('(WORKFLOW_IS_IN_STEP(o.status, \'delivering\') = true)')
            ->orWhere('(WORKFLOW_IS_IN_STEP(o.status, \'delivered\') = true)')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PrintingCampaign[]
     */
    public function createAdminOrderedExport(): iterable
    {
        return $this->createAdminExportQueryBuilder()
            ->where('o.order IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PrintingCampaign[]
     */
    public function createAdminAllExport(): iterable
    {
        return $this->createAdminExportQueryBuilder()->getQuery()->getResult();
    }

    private function createAdminExportQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'o', 'p', 'r')
            ->leftJoin('c.printingOrder', 'o')
            ->leftJoin('o.project', 'p')
            ->leftJoin('p.organization', 'r')
            ->orderBy('o.id', 'DESC')
            ->addOrderBy('c.id', 'ASC')
        ;
    }
}
