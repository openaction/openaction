<?php

namespace App\Controller\Console\Project;

use App\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/sentry')]
class SentryDemoController extends AbstractController
{
    #[Route('/demo', name: 'console_project_sentry_demo', methods: ['GET'])]
    public function __invoke(LoggerInterface $logger): Response
    {
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $logger->info('Sentry demo endpoint triggered', [
            'project' => $this->getProject()?->getName(),
            'organization' => $this->getOrganization()?->getName(),
        ]);

        throw new \RuntimeException('Sentry console demo exception');
    }
}
