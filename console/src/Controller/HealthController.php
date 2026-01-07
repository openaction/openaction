<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthController extends AbstractController
{
    #[Route('/health', name: 'health')]
    public function health(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('X-Robots-Tag', 'noindex');

        return $response;
    }
}
