<?php

namespace App\Repository\Community;

use App\Entity\Community\Contact;
use App\Entity\Community\TextingCampaign;
use App\Entity\Community\TextingCampaignMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use function Symfony\Component\String\u;

/**
 * @method TextingCampaignMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextingCampaignMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextingCampaignMessage[]    findAll()
 * @method TextingCampaignMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextingCampaignMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextingCampaignMessage::class);
    }

    public function createCampaignMessages(TextingCampaign $campaign, QueryBuilder $contactsQueryBuilder): void
    {
        $query = $contactsQueryBuilder->select('c.id')->getQuery();

        $sql = '
            INSERT INTO community_texting_campaigns_messages
            (id, contact_id, campaign_id, sent, delivered, bounced)
        ';

        $sql .= u($query->getSQL())->replace(
            'c0_.id AS id_0',
            'nextval(\'community_texting_campaigns_messages_id_seq\'), c0_.id, '.$campaign->getId().', false, false, false'
        );

        $params = [];
        foreach ($contactsQueryBuilder->getParameters() as $param) {
            $params[] = $param->getValue();
        }

        $query = $this->_em->getConnection()->prepare($sql);
        $query->executeStatement($params);
    }

    public function buildMessagesBatches(TextingCampaign $campaign, int $batchSize = 100): iterable
    {
        $iterator = $this->createQueryBuilder('m')
            ->select(
                'm.id AS id',
                'c.uuid',
                'c.email',
                'c.parsedContactPhone',
                'c.profileFormalTitle',
                'c.profileFirstName',
                'c.profileLastName',
                'c.profileGender',
                'c.profileNationality',
                'c.profileCompany',
                'c.profileJobTitle',
                'c.addressStreetLine1',
                'c.addressStreetLine2',
                'c.addressZipCode',
                'c.addressCity',
                'ac.code AS addressCountry'
            )
            ->leftJoin('m.contact', 'c')
            ->leftJoin('c.addressCountry', 'ac')
            ->where('m.campaign = :campaign')
            ->setParameter('campaign', $campaign->getId())
            ->andWhere('m.sent = FALSE')
            ->getQuery()
            ->toIterable()
        ;

        $i = 1;
        $batch = [];

        foreach ($iterator as $item) {
            $batch[] = $item;

            if (0 === $i % $batchSize) {
                yield $batch;
                $batch = [];
            }

            ++$i;
        }

        if ($batch) {
            yield $batch;
        }
    }

    /**
     * @return TextingCampaignMessage[]|iterable
     */
    public function findContactHistory(Contact $contact): iterable
    {
        return $this->createQueryBuilder('m')
            ->select('m', 'c')
            ->leftJoin('m.campaign', 'c')
            ->where('m.contact = :contact')
            ->setParameter('contact', $contact->getId())
            ->setMaxResults(50)
            ->getQuery()
            ->toIterable()
        ;
    }
}
