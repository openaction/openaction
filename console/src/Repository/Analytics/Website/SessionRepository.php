<?php

namespace App\Repository\Analytics\Website;

use App\Entity\Analytics\Website\Session;
use App\Entity\Project;
use App\Util\Date;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function removeOldSessions(string $beforeDate): void
    {
        $this->createQueryBuilder('s')
            ->delete()
            ->andWhere('s.endDate <= :before')
            ->setParameter('before', new \DateTime($beforeDate))
            ->getQuery()
            ->execute()
        ;
    }

    public function findProjectTrafficSessions(Project $project, \DateTime $startDate): iterable
    {
        $query = $this->_em->getConnection()->prepare("
            SELECT
               TO_TIMESTAMP(FLOOR((EXTRACT('epoch' from start_date) / ".Date::OneDay->value.')) * '.Date::OneDay->value.') as date,
               COUNT(*) AS users,
               SUM(paths_count) AS page_views
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ?
            GROUP BY date
            ORDER BY date
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

    public function findProjectTrafficTotals(Project $project, \DateTime $startDate): array
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT COUNT(*) AS users, SUM(paths_count) AS page_views
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ?
        ');

        $result = $query->executeQuery([
            $project->getId(),
            $startDate->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        $data = $result->fetchAssociative();

        return [
            'users' => $data['users'] ?: 0,
            'page_views' => $data['page_views'] ?: 0,
        ];
    }

    public function findProjectTrafficPages(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'path', '
            SELECT json_array_elements_text(paths_flow) AS path, COUNT(*) as value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND original_referrer IS NOT NULL
            GROUP BY path
            ORDER BY value DESC
        ');
    }

    public function findProjectTrafficSources(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'original_referrer', '
            SELECT original_referrer, SUM(paths_count) AS value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND original_referrer IS NOT NULL
            GROUP BY original_referrer
            ORDER BY value DESC
        ');
    }

    public function findProjectTrafficCountries(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'country', '
            SELECT country, SUM(paths_count) AS value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND country IS NOT NULL
            GROUP BY country
            ORDER BY value DESC
        ');
    }

    public function findProjectTrafficPlatforms(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'platform', '
            SELECT platform, SUM(paths_count) AS value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND platform IS NOT NULL
            GROUP BY platform
            ORDER BY value DESC
        ');
    }

    public function findProjectTrafficBrowsers(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'browser', '
            SELECT browser, SUM(paths_count) AS value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND browser IS NOT NULL
            GROUP BY browser
            ORDER BY value DESC
        ');
    }

    public function findProjectTrafficUtmSources(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'utm_source', '
            SELECT utm_source, SUM(paths_count) AS value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND utm_source IS NOT NULL
            GROUP BY utm_source
            ORDER BY value DESC
        ');
    }

    public function findProjectTrafficUtmMediums(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'utm_medium', '
            SELECT utm_medium, SUM(paths_count) AS value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND utm_medium IS NOT NULL
            GROUP BY utm_medium
            ORDER BY value DESC
        ');
    }

    public function findProjectTrafficUtmCampaigns(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'utm_campaign', '
            SELECT utm_campaign, SUM(paths_count) AS value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND utm_campaign IS NOT NULL
            GROUP BY utm_campaign
            ORDER BY value DESC
        ');
    }

    public function findProjectTrafficUtmContents(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'utm_content', '
            SELECT utm_content, SUM(paths_count) AS value
            FROM analytics_website_sessions
            WHERE project_id = ? AND start_date >= ? AND start_date <= ? AND utm_content IS NOT NULL
            GROUP BY utm_content
            ORDER BY value DESC
        ');
    }

    public function findProjectEvents(Project $project, \DateTime $startDate): array
    {
        return $this->executeAnalyticsQuery($project, $startDate, 'name', '
            SELECT name, COUNT(*) AS value
            FROM analytics_website_events
            WHERE project_id = ? AND date >= ? AND date <= ?
            GROUP BY name
            ORDER BY value DESC
        ');
    }

    public function findAdminTrafficSessions(\DateTime $startDate): iterable
    {
        $query = $this->_em->getConnection()->prepare("
            SELECT
               TO_TIMESTAMP(FLOOR((EXTRACT('epoch' from start_date) / ".Date::OneDay->value.')) * '.Date::OneDay->value.') as date,
               COUNT(*) AS users,
               SUM(paths_count) AS page_views
            FROM analytics_website_sessions
            WHERE start_date >= ? AND start_date <= ?
            GROUP BY date
            ORDER BY date
        ');

        $result = $query->executeQuery([
            $startDate->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        while ($row = $result->fetchAssociative()) {
            yield $row;
        }
    }

    public function findAdminTrafficTotals(\DateTime $startDate): array
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT COUNT(*) AS users, SUM(paths_count) AS page_views
            FROM analytics_website_sessions
            WHERE start_date >= ? AND start_date <= ?
        ');

        $result = $query->executeQuery([
            $startDate->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        return $result->fetchAssociative();
    }

    public function findAdminTrafficProjects(\DateTime $startDate): iterable
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT o.name AS organization, p.name AS project, COUNT(s.id) as value
            FROM analytics_website_sessions s
            LEFT JOIN projects p ON p.id = s.project_id 
            LEFT JOIN organizations o ON o.id = p.organization_id 
            WHERE start_date >= ? AND start_date <= ?
            GROUP BY organization, project
            ORDER BY value DESC
        ');

        $result = $query->executeQuery([
            $startDate->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        foreach ($result->fetchAllAssociative() as $row) {
            yield $row['organization'].' | '.$row['project'] => $row['value'];
        }
    }

    private function executeAnalyticsQuery(Project $project, \DateTime $startDate, string $indexBy, string $sql): array
    {
        $query = $this->_em->getConnection()->prepare($sql);

        $data = $query->executeQuery([
            $project->getId(),
            $startDate->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        $result = [];
        while ($row = $data->fetchAssociative()) {
            $result[$row[$indexBy]] = $row['value'];
        }

        return $result;
    }
}
