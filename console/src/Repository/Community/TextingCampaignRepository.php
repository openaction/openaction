<?php

namespace App\Repository\Community;

use App\Community\ContactViewBuilder;
use App\Entity\Area;
use App\Entity\Community\Tag;
use App\Entity\Community\TextingCampaign;
use App\Entity\Community\TextingCampaignMessage;
use App\Entity\Project;
use App\Form\Community\Model\TextingCampaignMetaData;
use App\Repository\Util\GridSearchRepositoryTrait;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\PhoneNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TextingCampaign|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextingCampaign|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextingCampaign|null findOneByBase62Uid(string $base62Uid)
 * @method TextingCampaign[]    findAll()
 * @method TextingCampaign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextingCampaignRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;
    use GridSearchRepositoryTrait;

    private ContactViewBuilder $contactViewBuilder;

    public function __construct(ManagerRegistry $registry, ContactViewBuilder $contactViewBuilder)
    {
        parent::__construct($registry, TextingCampaign::class);

        $this->contactViewBuilder = $contactViewBuilder;
    }

    /**
     * @return TextingCampaign[]
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

    public function findAllSentPaginator(Project $project, int $currentPage, int $limit = 30): Paginator
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

    public function findStats(TextingCampaign $campaign): array
    {
        $data = $this->_em->createQueryBuilder()
            ->select(
                'COUNT(m) AS total',
                'SUM(CASE WHEN m.sent = true THEN 1 ELSE 0 END) AS sent',
            )
            ->from(TextingCampaignMessage::class, 'm')
            ->where('m.campaign = :campaign')
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getArrayResult()
        ;

        return [
            'total' => (int) ($data[0]['total'] ?? 0),
            'sent' => (int) ($data[0]['sent'] ?? 0),
        ];
    }

    public function updateFilters(TextingCampaign $campaign, TextingCampaignMetaData $data): void
    {
        $this->_em->transactional(function () use ($campaign, $data) {
            $metadata = $this->_em->getClassMetadata(TextingCampaign::class);

            // Clear old tags
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['tagsFilter']['joinTable']['name'])
                ->where('texting_campaign_id = :campaign')
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
                ->where('texting_campaign_id = :campaign')
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

    public function updateContactsFilter(TextingCampaign $campaign, TextingCampaignMetaData $data): void
    {
        $validPhones = [];

        if ($parsedPhones = $data->parseContactsFilter()) {
            $contacts = $this->contactViewBuilder
                ->onlySmsSubscribers()
                ->inProject($campaign->getProject())
                ->withPhones($parsedPhones)
                ->createQueryBuilder()
                ->select('c.parsedContactPhone')
                ->getQuery()
                ->getArrayResult()
            ;

            foreach ($contacts as $contact) {
                $validPhones[] = PhoneNumber::format($contact['parsedContactPhone']);
            }
        }

        $campaign->applyContactsFilterUpdate(array_filter($validPhones));

        $this->_em->persist($campaign);
        $this->_em->flush();
    }

    public function searchReport(TextingCampaign $campaign, array $params): iterable
    {
        $qb = $this->_em->createQueryBuilder()
            ->from(TextingCampaignMessage::class, 'm')
            ->leftJoin('m.contact', 'c')
            ->leftJoin('m.campaign', 'e')
            ->leftJoin('c.area', 'a')
            ->leftJoin('c.metadataTags', 't')
            ->where('e.id = :campaign')
            ->setParameter('campaign', $campaign)
        ;

        $fieldsMap = [
            'contactPhone' => 'c.contactPhone',
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

        // Add sorting
        $fieldsMap['sent_at'] = 'sent';

        foreach ($params['sort'] as $sort) {
            if ($mappedField = $fieldsMap[$sort['colId']] ?? null) {
                $selectQb->addOrderBy($mappedField, 'desc' === $sort['sort'] ? 'DESC' : 'ASC');
            }
        }

        $selectQb->addOrderBy('c.createdAt', 'DESC');
        $selectQb->addOrderBy('c.contactPhone', 'ASC');

        // Add limits
        $selectQb
            ->setFirstResult($params['start'])
            ->setMaxResults($params['end'] - $params['start'])
        ;

        return [
            'total' => $totalCount,
            'page' => $selectQb->getQuery()->getResult(),
        ];
    }
}
