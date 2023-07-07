<?php

namespace App\Repository\Community;

use App\Entity\Community\PrintingCampaignUniqueScan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrintingCampaignUniqueScan|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintingCampaignUniqueScan|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintingCampaignUniqueScan[]    findAll()
 * @method PrintingCampaignUniqueScan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintingCampaignUniqueScanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintingCampaignUniqueScan::class);
    }
}
