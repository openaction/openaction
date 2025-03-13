<?php

namespace App\Repository\Community;

use App\Entity\Community\Contact;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Community\EmailingCampaignMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use function Symfony\Component\String\u;

/**
 * @method EmailingCampaignMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailingCampaignMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailingCampaignMessage[]    findAll()
 * @method EmailingCampaignMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailingCampaignMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailingCampaignMessage::class);
    }

    public function createCampaignMessages(EmailingCampaign $campaign, QueryBuilder $contactsQueryBuilder, bool $sent = false)
    {
        $query = $contactsQueryBuilder->select('c.id')->getQuery();

        $sql = '
            INSERT INTO community_emailing_campaigns_messages
            (id, contact_id, campaign_id, sent, bounced, opened, clicked)
        ';

        $sql .= u($query->getSQL())->replace(
            'c0_.id AS id_0',
            'nextval(\'community_emailing_campaigns_messages_id_seq\'), c0_.id, '.$campaign->getId().', '.($sent ? 'true' : 'false').', false, false, false'
        );

        $sql .= ' ON CONFLICT DO NOTHING';

        $params = [];
        foreach ($contactsQueryBuilder->getParameters() as $param) {
            $params[] = $param->getValue();
        }

        $query = $this->_em->getConnection()->prepare($sql);
        $query->executeStatement($params);
    }

    public function buildMessagesBatches(EmailingCampaign $campaign, int $batchSize = 100): iterable
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
     * @return EmailingCampaignMessage[]|iterable
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
