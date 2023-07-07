<?php

namespace App\Repository\Community;

use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingCampaignUniqueDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrintingCampaignUniqueDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintingCampaignUniqueDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintingCampaignUniqueDocument[]    findAll()
 * @method PrintingCampaignUniqueDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintingCampaignUniqueDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintingCampaignUniqueDocument::class);
    }

    public function createCampaignDocuments(PrintingCampaign $campaign, array $serialNumbers)
    {
        $values = [];
        foreach ($serialNumbers as $serialNumber) {
            $values[] = sprintf(
                '(%s, %s, %s, NULL)',
                'nextval(\'community_printing_campaigns_unique_documents_id_seq\')',
                $campaign->getId(),
                $serialNumber,
            );
        }

        $this->_em->getConnection()->executeStatement('
            INSERT INTO community_printing_campaigns_unique_documents (id, campaign_id, serial_number, label)
            VALUES '.implode(', ', $values)
        );
    }
}
