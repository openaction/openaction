<?php

namespace App\Repository\Community;

use App\Entity\Community\PrintingCampaignCommonScan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrintingCampaignCommonScan|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintingCampaignCommonScan|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintingCampaignCommonScan[]    findAll()
 * @method PrintingCampaignCommonScan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintingCampaignCommonScanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintingCampaignCommonScan::class);
    }
}
