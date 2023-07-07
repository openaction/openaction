<?php

namespace App\Repository\Community;

use App\Community\ContactViewBuilder;
use App\Entity\Area;
use App\Entity\Community\PhoningCampaign;
use App\Entity\Community\Tag;
use App\Entity\Project;
use App\Form\Community\Printing\Model\PhoningCampaignMetaData;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhoningCampaign|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhoningCampaign|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhoningCampaign|null findOneByBase62Uid(string $base62Uid)
 * @method PhoningCampaign[]    findAll()
 * @method PhoningCampaign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoningCampaignRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    private ContactViewBuilder $contactViewBuilder;

    public function __construct(ManagerRegistry $registry, ContactViewBuilder $contactViewBuilder)
    {
        parent::__construct($registry, PhoningCampaign::class);

        $this->contactViewBuilder = $contactViewBuilder;
    }

    /**
     * @return PhoningCampaign[]
     */
    public function findAllDrafts(Project $project)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.project = :project')
            ->andWhere('c.startAt IS NULL')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('project', $project->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PhoningCampaign[]
     */
    public function findAllActive(Project $project): iterable
    {
        return $this->createAllActiveQueryBuilder($project)->getQuery()->getResult();
    }

    public function createAllActiveQueryBuilder(Project $project): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.project = :project')
            ->andWhere('c.startAt <= :now')
            ->andWhere('DATE_ADD(c.startAt, c.endAfter, \'hour\') >= :now')
            ->orderBy('c.startAt', 'DESC')
            ->setParameter('project', $project)
            ->setParameter('now', new \DateTime())
        ;
    }

    /**
     * @return PhoningCampaign[]
     */
    public function findAllFinishedPaginator(Project $project, int $currentPage, int $limit = 30): Paginator
    {
        return new Paginator(
            $this->createAllFinishedQueryBuilder($project)
                ->setMaxResults($limit)
                ->setFirstResult(($currentPage - 1) * $limit)
                ->getQuery()
        );
    }

    public function createAllFinishedQueryBuilder(Project $project): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.project = :project')
            ->andWhere('c.startAt <= :now')
            ->andWhere('DATE_ADD(c.startAt, c.endAfter, \'hour\') < :now')
            ->orderBy('c.startAt', 'DESC')
            ->setParameter('project', $project)
            ->setParameter('now', new \DateTime())
        ;
    }

    public function updateFilters(PhoningCampaign $campaign, PhoningCampaignMetaData $data)
    {
        $this->_em->transactional(function () use ($campaign, $data) {
            $metadata = $this->_em->getClassMetadata(PhoningCampaign::class);

            // Clear old tags
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['tagsFilter']['joinTable']['name'])
                ->where('phoning_campaign_id = :campaign')
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
                ->where('phoning_campaign_id = :campaign')
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

    public function updateContactsFilter(PhoningCampaign $campaign, PhoningCampaignMetaData $data): void
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
}
