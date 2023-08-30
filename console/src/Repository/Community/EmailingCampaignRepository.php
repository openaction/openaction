<?php

namespace App\Repository\Community;

use App\Community\ContactViewBuilder;
use App\Entity\Area;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Community\EmailingCampaignMessage;
use App\Entity\Community\EmailingCampaignMessageLog;
use App\Entity\Community\Tag;
use App\Entity\Project;
use App\Form\Community\Model\EmailingCampaignMetaData;
use App\Repository\Util\GridSearchRepositoryTrait;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailingCampaign|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailingCampaign|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailingCampaign|null findOneByBase62Uid(string $base62Uid)
 * @method EmailingCampaign[]    findAll()
 * @method EmailingCampaign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailingCampaignRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;
    use GridSearchRepositoryTrait;

    private ContactViewBuilder $contactViewBuilder;

    public function __construct(ManagerRegistry $registry, ContactViewBuilder $contactViewBuilder)
    {
        parent::__construct($registry, EmailingCampaign::class);

        $this->contactViewBuilder = $contactViewBuilder;
    }

    /**
     * @return EmailingCampaign[]
     */
    public function findAllDrafts(Project $project)
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.project = :project')
            ->andWhere('e.sentAt IS NULL')
            ->setParameter('project', $project->getId())
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllSentPaginator(Project $project, int $currentPage, int $limit = 10): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder('c')
                ->select('c')
                ->where('c.sentAt IS NOT NULL')
                ->andWhere('c.project = :project')
                ->setParameter('project', $project)
                ->orderBy('c.sentAt', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult(($currentPage - 1) * $limit)
                ->getQuery()
        );
    }

    public function findStats(EmailingCampaign $campaign): array
    {
        $data = $this->_em->createQueryBuilder()
            ->select(
                'COUNT(m) AS total',
                'SUM(CASE WHEN m.sent = true THEN 1 ELSE 0 END) AS sent',
                'SUM(CASE WHEN m.opened = true THEN 1 ELSE 0 END) AS opened',
                'SUM(CASE WHEN m.clicked = true THEN 1 ELSE 0 END) AS clicked',
            )
            ->from(EmailingCampaignMessage::class, 'm')
            ->where('m.campaign = :campaign')
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getArrayResult()
        ;

        return [
            'total' => (int) ($data[0]['total'] ?? 0),
            'sent' => (int) ($data[0]['sent'] ?? 0),
            'opened' => (int) ($data[0]['opened'] ?? 0),
            'clicked' => (int) ($data[0]['clicked'] ?? 0),
        ];
    }

    public function updateFilters(EmailingCampaign $campaign, EmailingCampaignMetaData $data)
    {
        $this->_em->transactional(function () use ($campaign, $data) {
            $metadata = $this->_em->getClassMetadata(EmailingCampaign::class);

            // Clear old tags
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['tagsFilter']['joinTable']['name'])
                ->where('emailing_campaign_id = :campaign')
                ->setParameter('campaign', $campaign->getId())
                ->execute()
            ;

            // Create new tags
            $campaign->getTagsFilter()->clear();
            foreach ($data->parseTagsFilter() as $tagData) {
                if (isset($tagData['id']) && $tag = $this->_em->find(Tag::class, $tagData['id'])) {
                    $campaign->getTagsFilter()->add($tag);
                }
            }

            $this->_em->persist($campaign);
            $this->_em->flush();

            // Clear old areas
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['areasFilter']['joinTable']['name'])
                ->where('emailing_campaign_id = :campaign')
                ->setParameter('campaign', $campaign->getId())
                ->execute()
            ;

            // Create new areas
            $campaign->getAreasFilter()->clear();
            foreach ($data->parseAreasFilterIds() as $id) {
                $campaign->getAreasFilter()->add($this->_em->find(Area::class, $id));
            }

            $this->_em->persist($campaign);
            $this->_em->flush();
        });

        $this->updateContactsFilter($campaign, $data);
    }

    public function updateContactsFilter(EmailingCampaign $campaign, EmailingCampaignMetaData $data): void
    {
        $parsedEmails = $data->parseContactsFilter();
        $validEmails = [];

        if ($parsedEmails) {
            $contacts = $this->contactViewBuilder
                ->onlyNewsletterSubscribers()
                ->inProject($campaign->getProject())
                ->withEmails($parsedEmails)
                ->createQueryBuilder()
                ->select('c.email')
                ->getQuery()
                ->getArrayResult()
            ;

            foreach ($contacts as $contact) {
                $validEmails[] = $contact['email'];
            }
        }

        $campaign->applyContactsFilterUpdate($validEmails);

        $this->_em->persist($campaign);
        $this->_em->flush();
    }

    public function searchReport(EmailingCampaign $campaign, array $params): iterable
    {
        $qb = $this->_em->createQueryBuilder()
            ->from(EmailingCampaignMessage::class, 'm')
            ->leftJoin('m.contact', 'c')
            ->leftJoin('m.campaign', 'e')
            ->leftJoin('c.area', 'a')
            ->leftJoin('c.metadataTags', 't')
            ->where('e.id = :campaign')
            ->setParameter('campaign', $campaign)
        ;

        $fieldsMap = [
            'email' => 'c.email',
            'firstName' => 'c.profileFirstName',
            'lastName' => 'c.profileLastName',
        ];

        // Add filters
        $this->k = 0;
        foreach ($params['filter'] as $colId => $filter) {
            if (!$mappedField = $fieldsMap[$colId] ?? null) {
                continue;
            }

            $op = $this->createAgGridFilterOperation($mappedField, $filter);
            $qb->andWhere($op['expr']);

            foreach ($op['params'] as $key => $value) {
                $qb->setParameter($key, $value);
            }
        }

        // Count results
        $countQb = clone $qb;
        $totalCount = $countQb->select('COUNT(DISTINCT m)')->getQuery()->getSingleScalarResult();

        // Select results
        $selectQb = clone $qb;
        $selectQb->select('m AS message', 'e', 'c', 'a', 't');

        // Add open and click counts
        $opensQuery = $this->_em->createQueryBuilder()
            ->select('COUNT(lo)')
            ->from(EmailingCampaignMessageLog::class, 'lo')
            ->where('lo.message = m.id')
            ->andWhere('lo.type = :open')
            ->setParameter('type', EmailingCampaignMessageLog::TYPE_OPEN)
            ->getQuery()
            ->getDQL()
        ;

        $selectQb->addSelect('('.$opensQuery.') AS opens');
        $selectQb->setParameter('open', EmailingCampaignMessageLog::TYPE_OPEN);

        $clicksQuery = $this->_em->createQueryBuilder()
            ->select('COUNT(lc)')
            ->from(EmailingCampaignMessageLog::class, 'lc')
            ->where('lc.message = m.id')
            ->andWhere('lc.type = :click')
            ->getQuery()
            ->getDQL()
        ;

        $selectQb->addSelect('('.$clicksQuery.') AS clicks');
        $selectQb->setParameter('click', EmailingCampaignMessageLog::TYPE_CLICK);

        // Add sorting
        $fieldsMap['opens'] = 'opens';
        $fieldsMap['clicks'] = 'clicks';

        foreach ($params['sort'] as $sort) {
            if ($mappedField = $fieldsMap[$sort['colId']] ?? null) {
                $selectQb->addOrderBy($mappedField, 'desc' === $sort['sort'] ? 'DESC' : 'ASC');
            }
        }

        $selectQb->addOrderBy('clicks', 'DESC');
        $selectQb->addOrderBy('opens', 'DESC');
        $selectQb->addOrderBy('c.createdAt', 'DESC');
        $selectQb->addOrderBy('c.email', 'ASC');

        // Add limits
        $selectQb
            ->setFirstResult($params['start'])
            ->setMaxResults($params['end'] - $params['start'])
        ;

        return [
            'total' => $totalCount,
            'page' => (new Paginator($selectQb))->getIterator(),
        ];
    }

    public function getExportData(EmailingCampaign $campaign): iterable
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT DISTINCT ON (c.email) 
                c.email,
                (
                    SELECT COUNT(*) 
                    FROM community_emailing_campaigns_messages_logs lo
                    WHERE lo.message_id = m.id AND lo.type = \'open\'
                ) AS opens,
                (
                    SELECT COUNT(*) 
                    FROM community_emailing_campaigns_messages_logs lc
                    WHERE lc.message_id = m.id AND lc.type = \'click\'
                ) AS clicks,
                c.uuid AS id,
                a.name AS area,
                c.profile_formal_title,
                c.profile_first_name,
                c.profile_middle_name,
                c.profile_last_name,
                c.profile_birthdate,
                c.profile_company,
                c.profile_job_title,
                c.contact_phone,
                c.contact_work_phone,
                c.parsed_contact_phone,
                c.parsed_contact_work_phone,
                c.social_facebook,
                c.social_twitter,
                c.social_linked_in,
                c.social_telegram,
                c.social_whatsapp,
                c.address_street_line1,
                c.address_street_line2,
                c.address_zip_code,
                c.address_city,
                ac.name AS address_country,
                (CASE WHEN c.account_password IS NOT NULL THEN 1 ELSE 0 END) AS is_member,
                (CASE WHEN c.settings_receive_newsletters = true THEN 1 ELSE 0 END) AS settings_receive_newsletters,
                (CASE WHEN c.settings_receive_sms = true THEN 1 ELSE 0 END) AS settings_receive_sms,
                (CASE WHEN c.settings_receive_calls = true THEN 1 ELSE 0 END) AS settings_receive_calls,
                t.tags_array AS metadata_tags,
                c.metadata_comment,
                c.created_at
            FROM community_emailing_campaigns_messages m
            LEFT JOIN community_contacts c ON m.contact_id = c.id
            LEFT JOIN (
                SELECT ct.contact_id, string_agg(t.name, \', \') AS tags_array
                FROM community_contacts_tags ct
                JOIN community_tags t ON t.id = ct.tag_id
                GROUP BY ct.contact_id
            ) t ON t.contact_id = c.id
            LEFT JOIN json_array_elements_text(c.contact_additional_emails) AS cae ON true
            LEFT JOIN areas a ON c.area_id = a.id
            LEFT JOIN areas ac ON c.address_country_id = ac.id
            WHERE m.campaign_id = ?
        ');

        return $query->executeQuery([$campaign->getId()])->iterateAssociative();
    }
}
