<?php

namespace App\Analytics\Consumer;

use App\Entity\Analytics\Website\PageView;
use App\Entity\Analytics\Website\Session;
use App\Entity\Project;
use App\Repository\Analytics\Website\PageViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Build sessions aggregates using page views data. A session
 * is an aggregate of 30 minutes for a single hash.
 *
 * This handler builds the oldest session from page views, deletes
 * the original page views data and calls itself recursively until
 * all sessions for this project are consumed.
 */
#[AsMessageHandler]
final class BuildWebsiteSessionsHandler
{
    private EntityManagerInterface $em;
    private PageViewRepository $pageViewRepo;
    private MessageBusInterface $bus;
    private LoggerInterface $logger;

    private ?Project $project = null;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->pageViewRepo = $em->getRepository(PageView::class);
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function __invoke(BuildWebsiteSessionsMessage $message)
    {
        // If the project has been deleted, ignore messages
        if (!$this->project = $this->em->find(Project::class, $message->getProjectId())) {
            $this->logger->error('Project not found for website sessions creation.', [
                'project_id' => $message->getProjectId(),
            ]);

            return;
        }

        // Build the session in a transaction to avoid race conditions between handlers
        $aggregatedViews = $this->em->wrapInTransaction([$this, 'buildSession']);

        // If there was a session to build, continue recursively
        if (is_array($aggregatedViews)) {
            $this->bus->dispatch(new BuildWebsiteSessionsMessage($message->getProjectId()));
        }
    }

    public function buildSession(): ?array
    {
        // Find the oldest session page views
        if (!$pageViews = $this->pageViewRepo->findOldestSessionPageViewsFor($this->project)) {
            return null;
        }

        // Build and persist the session
        $this->em->persist(Session::createFromPageViews($pageViews));

        // Clear aggregated page views
        $this->pageViewRepo->removeOldestSessionPageViews($pageViews);

        return $pageViews;
    }
}
