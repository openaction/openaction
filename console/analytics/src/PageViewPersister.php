<?php

namespace Analytics;

class PageViewPersister
{
    private const PAGE_VIEWS_ENTITIES = [
        'post' => 'website_posts',
        'page' => 'website_pages',
        'form' => 'website_forms',
        'event' => 'website_events',
        'manifesto' => 'website_manifestos_topics',
        'trombinoscope' => 'website_trombinoscope_persons',
    ];

    private string $dsn;

    public function __construct(string $dsn)
    {
        $this->dsn = $dsn;
    }

    public function persist(string $projectUuid, string $ip, string $path, ?string $platform, ?string $browser, ?string $country, ?string $referrerDomain, ?string $referrerPath, ?string $utmSource, ?string $utmMedium, ?string $utmCampaign, ?string $utmContent)
    {
        $query = $this->createConnection()->prepare("
            INSERT INTO analytics_website_page_views (id, project_id, hash, path, platform, browser, country, referrer, referrer_path, utm_source, utm_medium, utm_campaign, utm_content, date)
                SELECT nextval('analytics_website_page_views_id_seq'), p.id, md5(?)::uuid, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                FROM projects p
                WHERE uuid = ?
        ");

        try {
            $query->execute([
                $ip,
                $path,
                $platform,
                $browser,
                $country,
                $referrerDomain,
                $referrerPath,
                $utmSource,
                $utmMedium,
                $utmCampaign,
                $utmContent,
                (new \DateTime())->format('Y-m-d H:i:s'),
                $projectUuid
            ]);
        } catch (\Exception) {
            // no-op
        }
    }

    public function incrementPageViews(string $entityType, string $entityUuid)
    {
        if (!$table = self::PAGE_VIEWS_ENTITIES[$entityType] ?? null) {
            return;
        }

        $query = $this->createConnection()->prepare('UPDATE '.$table.' SET page_views = page_views + 1 WHERE uuid = ?');

        try {
            $query->execute([$entityUuid]);
        } catch (\Exception $e) {
            // no-op
        }
    }

    public function persistEvent(string $projectUuid, string $ip, string $eventName)
    {
        $query = $this->createConnection()->prepare("
            INSERT INTO analytics_website_events (id, project_id, hash, name, date)
                SELECT nextval('analytics_website_events_id_seq'), p.id, md5(?)::uuid, ?, ?
                FROM projects p
                WHERE uuid = ?
        ");

        try {
            $query->execute([$ip, $eventName, (new \DateTime())->format('Y-m-d H:i:s'), $projectUuid]);
        } catch (\Exception) {
            // no-op
        }
    }

    private function createConnection(): \PDO
    {
        $params = parse_url($this->dsn);

        $db = new \PDO('pgsql:dbname='.trim($params['path'], '/').';host='.$params['host'].';port='.$params['port'], $params['user'], $params['pass']);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
