<?php

namespace App\Analytics\Consumer;

use App\Entity\Project;
use App\Repository\Analytics\Website\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Clear sessions stats older than 3 years.
 */
#[AsMessageHandler]
final class ClearWebsiteSessionsHandler
{
    private EntityManagerInterface $em;
    private SessionRepository $repo;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, SessionRepository $repo, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->logger = $logger;
    }

    public function __invoke(ClearWebsiteSessionsMessage $message)
    {
        // If the project has been deleted, ignore messages
        if (!$project = $this->em->find(Project::class, $message->getProjectId())) {
            $this->logger->error('Project not found for website sessions removal.', [
                'project_id' => $message->getProjectId(),
            ]);

            return;
        }

        $this->repo->removeOldSessions($project, '3 years ago');
    }
}
