<?php

namespace App\Controller;

use MaxMind\Db\Reader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/country", name="api_country")
     */
    public function country(Request $request)
    {
        $reader = new Reader($this->getParameter('kernel.project_dir').'/data/GeoLite2-Country.mmdb');
        $record = $reader->get($request->headers->get('CF-Connecting-IP', $request->getClientIp()));

        return $this->json([
            'country' => strtoupper($record['country']['iso_code'] ?? 'FR'),
        ]);
    }
}
