<?php

namespace App\Command\Analytics;

use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:analytics:sessions:build',
    description: 'Build session aggregates from page views.',
)]
class BuildWebsitesSessionsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Building sessions ...');

        $progressBar = new ProgressBar($output);
        $progressBar->start();

        $db = $this->em->getConnection();

        while ($session = $this->buildOldestSession()) {
            $db->transactional(function () use ($session, $db) {
                $pageViewsIds = Json::decode($session['page_views_ids']);
                unset($session['page_views_ids']);

                if (!$pageViewsIds) {
                    return;
                }

                $db->insert('analytics_website_sessions', $session);

                $qb = $db->createQueryBuilder();
                $qb->delete('analytics_website_page_views')
                    ->where($qb->expr()->in('id', $pageViewsIds))
                    ->executeStatement();
            });

            $progressBar->advance();

            // Deallocate prepared statements to avoid reaching the max number of prepared statements threshold
            $db->executeStatement('DEALLOCATE ALL');
        }

        $progressBar->finish();

        $io->success('Sessions built');

        return Command::SUCCESS;
    }

    private function buildOldestSession(): ?array
    {
        $result = $this->em->getConnection()->executeQuery('
            WITH first_view AS (
                SELECT pv.hash, pv.date AS first_date
                FROM analytics_website_page_views pv
                WHERE pv.date < current_timestamp - INTERVAL \'1 hour\'
                ORDER BY pv.date
                LIMIT 1
            ),
            batch AS (
                SELECT v.*
                FROM analytics_website_page_views v
                JOIN first_view f
                ON v.hash = f.hash
                AND v.date <= f.first_date + INTERVAL \'30 minutes\'
            ),
            initial AS (
                -- the earliest view in the batch
                SELECT b.*
                FROM batch b
                ORDER BY b.date
                LIMIT 1
            ),
            first_utm AS (
                -- the first view in the batch that has a utm_source ("if initial has none, take first that has one")
                SELECT b.utm_source, b.utm_medium, b.utm_campaign, b.utm_content
                FROM batch b
                WHERE b.utm_source IS NOT NULL
                ORDER BY b.date
                LIMIT 1
            )
            SELECT
                i.project_id,
                p.organization_id,
                i.hash,
                (
                    SELECT json_agg(b2.path ORDER BY b2.date)
                    FROM batch b2
                ) AS paths_flow,
                (
                    SELECT COUNT(*)
                    FROM batch b3
                ) AS paths_count,
                i.platform,
                i.browser,
                i.country,
                i.referrer AS original_referrer,
                (
                    SELECT MIN(b4.date)
                    FROM batch b4
                ) AS start_date,
                (
                    SELECT MAX(b5.date)
                    FROM batch b5
                ) AS end_date,
                COALESCE(i.utm_source, fu.utm_source) AS utm_source,
                COALESCE(i.utm_medium, fu.utm_medium) AS utm_medium,
                COALESCE(i.utm_campaign, fu.utm_campaign) AS utm_campaign,
                COALESCE(i.utm_content, fu.utm_content) AS utm_content,
                (
                    SELECT json_agg(b6.id ORDER BY b6.date)
                    FROM batch b6
                ) AS page_views_ids
            FROM initial i
            JOIN public.projects p ON p.id = i.project_id
            LEFT JOIN first_utm fu ON TRUE
        ');

        return $result->fetchAssociative() ?: null;
    }
}
