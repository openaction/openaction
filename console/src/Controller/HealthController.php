<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class HealthController extends AbstractController
{
    private Connection $db;
    private CacheInterface $redis;

    public function __construct(Connection $db, CacheInterface $redis)
    {
        $this->db = $db;
        $this->redis = $redis;
    }

    #[Route('/health/G7PjZtNL7zZenQY23OoCax2Ng0bV8cvl', name: 'health')]
    public function health()
    {
        $checks = [
            'Database' => $this->isDatabaseHealthy(),
            'Redis' => $this->isRedisHealthy(),
        ];

        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('X-Robots-Tag', 'noindex');

        $content = [];
        foreach ($checks as $name => $check) {
            if (!$check) {
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $content[] = $name.': '.($check ? 'OK' : 'Down');
        }

        $response->setContent(implode("\n", $content));

        return $response;
    }

    private function isDatabaseHealthy(): bool
    {
        try {
            return false !== stripos($this->db->query('SELECT version()')->fetchOne(), 'PostgreSQL');
        } catch (\Throwable) {
            return false;
        }
    }

    private function isRedisHealthy(): bool
    {
        try {
            return $this->redis->get(time(), static fn () => true);
        } catch (\Throwable) {
            return false;
        }
    }
}
