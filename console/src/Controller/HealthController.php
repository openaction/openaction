<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthController extends AbstractController
{
    public function __construct(private readonly Connection $db)
    {
    }

    #[Route('/health', name: 'health')]
    public function health(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('X-Robots-Tag', 'noindex');

        if (!$this->isDatabaseHealthy()) {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent('KO');
        } else {
            $response->setContent('OK');
        }

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
}
