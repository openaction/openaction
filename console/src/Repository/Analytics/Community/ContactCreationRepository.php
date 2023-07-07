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

    public function getOrganizationDashboardStats(Organization $organization): array
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT c.project_id, COUNT(c.id) AS total, COUNT(CASE WHEN c.is_member THEN 1 END) AS members
            FROM analytics_community_contact_creations c
            WHERE c.organization_id = ?
            GROUP BY c.project_id
        ');

        $result = $query->executeQuery([$organization->getId()]);

        $stats = [];
        while ($row = $result->fetchAssociative()) {
            $stats[$row['project_id']] = [
                'contacts' => $row['total'],
                'members' => $row['members'],
            ];
        }

        return $stats;
    }

    /**
     * @param Organization[] $organizations
     */
    public function getPartnerDashboardStats(array $organizations): array
    {
        if (!$organizations) {
            return [];
        }

        $ids = [];
        foreach ($organizations as $orga) {
            $ids[] = (int) $orga->getId();
        }

        $query = $this->_em->getConnection()->prepare('
            SELECT c.organization_id, COUNT(DISTINCT c.contact_id) AS contacts
            FROM analytics_community_contact_creations c
            WHERE c.organization_id IN ('.implode(',', $ids).')
            GROUP BY c.organization_id
        ');

        $result = $query->executeQuery([]);

        $stats = [];
        while ($row = $result->fetchAssociative()) {
            $stats[$row['organization_id']] = $row['contacts'];
        }

        return $stats;
    }

    public function refreshOrganizationStats(Organization $organization): bool
    {
        $em = $this->_em;
        $id = $organization->getId();

        try {
            $em->wrapInTransaction(static function () use ($em, $id) {
                $em->getConnection()->executeStatement("
                DELETE FROM analytics_community_contact_creations WHERE organization_id = $id;
                
                -- Global projects 
                INSERT INTO analytics_community_contact_creations
                (id, organization_id, project_id, contact_id, is_member, has_phone, receives_newsletter, receives_sms, country, tags, gender, date)
                    SELECT
                        nextval('analytics_community_contact_creations_id_seq'),
                        p.organization_id as organization_id,
                        p.id as project_id,
                        c.id as contact_id,
                        c.account_password IS NOT NULL as is_member,
                        c.parsed_contact_phone IS NOT NULL as has_phone,
                        c.settings_receive_newsletters as receives_newsletter,
                        c.settings_receive_sms as receives_sms,
                        (SELECT country.code FROM areas country WHERE country.id = c.address_country_id) AS country,
                        (
                            SELECT json_agg(DISTINCT t.name)
                            FROM community_contacts_tags ct
                            LEFT JOIN community_tags t on ct.tag_id = t.id
                            WHERE ct.contact_id = c.id
                        ) AS tags,
                        c.profile_gender as gender,
                        c.created_at as date
                    FROM community_contacts c, projects p
                    WHERE c.organization_id = $id
                      AND p.organization_id = $id
                      AND p.id NOT IN (SELECT pa.project_id FROM projects_areas pa)
                      AND p.id NOT IN (SELECT pt.project_id FROM projects_tags pt)
                ;
                
                -- Local projects
                INSERT INTO analytics_community_contact_creations
                (id, organization_id, project_id, contact_id, is_member, has_phone, receives_newsletter, receives_sms, country, tags, gender, date)
                    SELECT
                        nextval('analytics_community_contact_creations_id_seq'),
                        organization_id,
                        project_id,
                        contact_id,
                        is_member,
                        has_phone,
                        receives_newsletter,
                        receives_sms,
                        contact_country_area.code AS country,
                        (
                            SELECT json_agg(DISTINCT t.name)
                            FROM community_contacts_tags ct
                            LEFT JOIN community_tags t on ct.tag_id = t.id
                            WHERE ct.contact_id = cc.contact_id
                        ) AS tags,
                        gender,
                        date
                    FROM (
                        SELECT
                            p.organization_id as organization_id,
                            p.id as project_id,
                            c.id as contact_id,
                            c.account_password IS NOT NULL as is_member,
                            c.parsed_contact_phone IS NOT NULL as has_phone,
                            c.settings_receive_newsletters as receives_newsletter,
                            c.settings_receive_sms as receives_sms,
                            c.profile_gender as gender,
                            c.created_at as date,
                            c.area_id as contact_area_id,
                            c.address_country_id as contact_country_area_id,
                            pa.area_id as project_area_id
                        FROM community_contacts c, projects_areas pa
                        LEFT JOIN projects p on pa.project_id = p.id
                        WHERE c.organization_id = $id
                          AND p.organization_id = $id
                    ) cc
                    LEFT JOIN areas contact_area on contact_area.id = cc.contact_area_id
                    LEFT JOIN areas contact_country_area on contact_country_area.id = cc.contact_country_area_id
                    LEFT JOIN areas project_area on project_area.id = cc.project_area_id
                    WHERE contact_area.tree_left >= project_area.tree_left 
                      AND contact_area.tree_right <= project_area.tree_right
                ;

                -- Thematic projects
                INSERT INTO analytics_community_contact_creations
                (id, organization_id, project_id, contact_id, is_member, has_phone, receives_newsletter, receives_sms, country, tags, gender, date)
                    SELECT
                        nextval('analytics_community_contact_creations_id_seq'),
                        organization_id,
                        project_id,
                        contact_id,
                        is_member,
                        has_phone,
                        receives_newsletter,
                        receives_sms,
                        (SELECT country.code FROM areas country WHERE country.id = cc.contact_country_area_id) AS country,
                        (
                            SELECT json_agg(DISTINCT t.name)
                            FROM community_contacts_tags ct
                            LEFT JOIN community_tags t on ct.tag_id = t.id
                            WHERE ct.contact_id = cc.contact_id
                        ) AS tags,
                        gender,
                        date
                    FROM (
                        SELECT
                            p.organization_id as organization_id,
                            p.id as project_id,
                            c.id as contact_id,
                            c.account_password IS NOT NULL as is_member,
                            c.parsed_contact_phone IS NOT NULL as has_phone,
                            c.settings_receive_newsletters as receives_newsletter,
                            c.settings_receive_sms as receives_sms,
                            c.profile_gender as gender,
                            c.created_at as date,
                            c.address_country_id as contact_country_area_id
                        FROM community_contacts c, projects_tags pt
                        LEFT JOIN projects p on pt.project_id = p.id
                        WHERE c.organization_id = $id
                          AND p.organization_id = $id
                          AND pt.tag_id IN (SELECT cct.tag_id FROM community_contacts_tags cct WHERE cct.contact_id = c.id)
                        GROUP BY p.organization_id, p.id, c.id, c.account_password, c.parsed_contact_phone, 
                                 c.settings_receive_newsletters, c.settings_receive_sms, c.profile_gender, 
                                 c.created_at, c.address_country_id
                        HAVING COUNT(DISTINCT pt.tag_id) = (SELECT COUNT(DISTINCT spt.tag_id) FROM projects_tags spt WHERE spt.project_id = p.id)
                    ) cc
                ;
            ");
            });
        } catch (\PDOException $e) {
            // Handle locks gracefully
            if (str_contains($e->getMessage(), 'deadlock')) {
                return false;
            }

            throw $e;
        }

        return true;
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
