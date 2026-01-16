<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SentryDemoController extends AbstractController
{
    #[Route('/_internal/sentry-demo', name: 'sentry_demo', methods: ['GET'])]
    public function __invoke(Request $request, LoggerInterface $logger): Response
    {
        if ($request->query->get('token') !== $this->getApiToken()) {
            throw $this->createNotFoundException();
        }

        $project = $this->getProject();

        $logger->info('Sentry demo endpoint triggered', [
            'project' => $project?->name ?? null,
        ]);

        throw new \RuntimeException('Sentry public demo exception');
    }
}
