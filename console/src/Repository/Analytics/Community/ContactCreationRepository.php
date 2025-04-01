<?php

namespace App\Repository\Analytics\Community;

use App\Entity\Analytics\Community\ContactCreation;
use App\Entity\Organization;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactCreation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactCreation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactCreation[]    findAll()
 * @method ContactCreation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactCreationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactCreation::class);
    }

    public function findProjectCommunityTotals(Project $project): array
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT 
               COUNT(*) AS contacts,
               COUNT(CASE WHEN is_member THEN 1 END) AS members,
               COUNT(CASE WHEN receives_newsletter THEN 1 END) AS newsletter_subscribers,
               COUNT(CASE WHEN receives_sms THEN 1 END) AS sms_subscribers
            FROM analytics_community_contact_creations
            WHERE project_id = ?
        ');

        return $query->executeQuery([$project->getId()])->fetchAssociative();
    }

    public function findProjectCommunityGrowth(Project $project, \DateTime $startDate, int $precision): iterable
    {
        $query = $this->_em->getConnection()->prepare("
            SELECT
               TO_TIMESTAMP(FLOOR((EXTRACT('epoch' from date) / ".$precision.')) * '.$precision.') as period,
               COUNT(*) AS new_contacts,
               COUNT(CASE WHEN is_member THEN 1 END) AS new_members
            FROM analytics_community_contact_creations
            WHERE project_id = ? AND date >= ? AND date <= ?
            GROUP BY period
            ORDER BY period
        ');

        $result = $query->executeQuery([
            $project->getId(),
            $startDate->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        while ($row = $result->fetchAssociative()) {
            yield $row;
        }
    }

    public function findProjectCommunityTags(Project $project): \Generator
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT json_array_elements_text(tags) AS tag, COUNT(*) as value 
            FROM analytics_community_contact_creations
            WHERE project_id = ? AND tags IS NOT NULL
            GROUP BY tag
            ORDER BY value DESC
        ');

        $result = $query->executeQuery([$project->getId()]);

        while ($row = $result->fetchAssociative()) {
            yield $row['tag'] => $row['value'];
        }
    }

    public function findProjectCommunityCountries(Project $project): \Generator
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT country, COUNT(*) AS value
            FROM analytics_community_contact_creations
            WHERE project_id = ? AND country IS NOT NULL
            GROUP BY country
            ORDER BY value DESC
        ');

        $result = $query->executeQuery([$project->getId()]);

        while ($row = $result->fetchAssociative()) {
            yield $row['country'] => $row['value'];
        }
    }

    public function findAdminCommunityTotals(): array
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT 
               COUNT(*) AS contacts,
               COUNT(CASE WHEN account_password IS NOT NULL THEN 1 END) AS members,
               COUNT(CASE WHEN settings_receive_newsletters THEN 1 END) AS newsletter_subscribers,
               COUNT(CASE WHEN settings_receive_sms THEN 1 END) AS sms_subscribers
            FROM community_contacts
        ');

        return $query->executeQuery([])->fetchAssociative();
    }

    public function findAdminCommunityGrowth(\DateTime $startDate, int $precision): iterable
    {
        $query = $this->_em->getConnection()->prepare("
            SELECT
               TO_TIMESTAMP(FLOOR((EXTRACT('epoch' from created_at) / ".$precision.')) * '.$precision.') as period,
               COUNT(*) AS new_contacts,
               COUNT(CASE WHEN account_password IS NOT NULL THEN 1 END) AS new_members
            FROM community_contacts
            WHERE created_at >= ? AND created_at <= ?
            GROUP BY period
            ORDER BY period
        ');

        $result = $query->executeQuery([
            $startDate->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        while ($row = $result->fetchAssociative()) {
            yield $row;
        }
    }

    public function findAdminCommunityCountries(): \Generator
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT country, COUNT(DISTINCT contact_id) AS value
            FROM analytics_community_contact_creations
            WHERE country IS NOT NULL
            GROUP BY country
            ORDER BY value DESC
        ');

        $result = $query->executeQuery([]);

        while ($row = $result->fetchAssociative()) {
            yield $row['country'] => $row['value'];
        }
    }

    public function findAdminCommunityOrganizations(): \Generator
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT o.name, COUNT(*) AS value
            FROM community_contacts c
            LEFT JOIN organizations o ON o.id = c.organization_id
            GROUP BY o.name
            ORDER BY value DESC
        ');

        $result = $query->executeQuery([]);

        while ($row = $result->fetchAssociative()) {
            yield $row['name'] => $row['value'];
        }
    }
}
