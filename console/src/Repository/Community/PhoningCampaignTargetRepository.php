<?php

namespace App\Repository\Community;

use App\Entity\Community\Contact;
use App\Entity\Community\PhoningCampaign;
use App\Entity\Community\PhoningCampaignCall;
use App\Entity\Community\PhoningCampaignTarget;
use App\Entity\Project;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use function Symfony\Component\String\u;

/**
 * @method PhoningCampaignTarget|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhoningCampaignTarget|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhoningCampaignTarget|null findOneByBase62Uid(string $base62Uid)
 * @method PhoningCampaignTarget[]    findAll()
 * @method PhoningCampaignTarget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoningCampaignTargetRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoningCampaignTarget::class);
    }

    public function createCampaignTargets(PhoningCampaign $campaign, QueryBuilder $contactsQueryBuilder): void
    {
        $query = $contactsQueryBuilder->select('c.id')->getQuery();

        $sql = '
            INSERT INTO community_phoning_campaigns_targets
            (id, contact_id, campaign_id, answer_id, uuid, created_at, updated_at)
        ';

        $sql .= u($query->getSQL())->replace(
            'c0_.id AS id_0',
            'nextval(\'community_phoning_campaigns_targets_id_seq\'), c0_.id, '.$campaign->getId().', 
            null, gen_random_uuid(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP'
        );

        $params = [];
        foreach ($contactsQueryBuilder->getParameters() as $param) {
            $params[] = $param->getValue();
        }

        $query = $this->_em->getConnection()->prepare($sql);
        $query->executeStatement($params);
    }

    public function findPhoningTarget(Contact $author, PhoningCampaign $campaign): ?PhoningCampaignTarget
    {
        $calledTargetsSubQuery = $this->_em->createQueryBuilder()
            ->select('DISTINCT st.id')
            ->from(PhoningCampaignCall::class, 'sc')
            ->leftJoin('sc.target', 'st')
            ->where('st.campaign = :campaign')
            ->getQuery()
            ->getDQL()
        ;

        $qb = $this->createQueryBuilder('t');

        return $qb
            ->select('t', 'c', 'ca')
            ->leftJoin('t.contact', 'c')
            ->leftJoin('t.calls', 'ca')
            ->andWhere($qb->expr()->notIn('t.id', $calledTargetsSubQuery))
            ->andWhere('t.campaign = :campaign')
            ->setParameter('campaign', $campaign)
            ->andWhere('t.contact != :author')
            ->setParameter('author', $author)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findActiveCampaignsProgress(Project $project): array
    {
        return $this->findCampaignsProgress(
            $this->_em->getRepository(PhoningCampaign::class)->createAllActiveQueryBuilder($project)
        );
    }

    public function findFinishedCampaignsProgress(Project $project): array
    {
        return $this->findCampaignsProgress(
            $this->_em->getRepository(PhoningCampaign::class)->createAllFinishedQueryBuilder($project)
        );
    }

    private function findCampaignsProgress(QueryBuilder $campaignsQb): array
    {
        $callsQb = $this->_em->createQueryBuilder()
            ->select('COUNT(ca.id)')
            ->from(PhoningCampaignCall::class, 'ca')
            ->where('ca.target = t.id')
        ;

        $qb = $this->createQueryBuilder('t');
        $qb->select(
            'IDENTITY(t.campaign) AS campaign_id',
            'COUNT(t.id) AS total',
            'SUM(CASE WHEN ('.$callsQb->getDQL().') > 0 THEN 1 ELSE 0 END) AS done'
        );

        $qb->where($qb->expr()->in('t.campaign', $campaignsQb->getDQL()));
        $qb->groupBy('campaign_id');
        $qb->setParameters($campaignsQb->getParameters());

        $data = $qb->getQuery()->getArrayResult();

        $result = [];
        foreach ($data as $row) {
            $result[$row['campaign_id']] = [
                'total' => $row['total'],
                'done' => $row['done'],
                'progress' => $row['total'] ? round(($row['done'] / $row['total']) * 100) : 0,
            ];
        }

        return $result;
    }
}
