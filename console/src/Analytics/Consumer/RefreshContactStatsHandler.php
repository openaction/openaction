<?php

namespace App\Analytics\Consumer;

use App\Repository\Analytics\Community\ContactCreationRepository;
use App\Repository\OrganizationRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Refresh organization stats for contacts.
 */
final class RefreshContactStatsHandler implements MessageHandlerInterface
{
    private OrganizationRepository $orgaRepo;
    private ContactCreationRepository $contactCreationRepo;
    private CacheItemPoolInterface $cache;
    private LoggerInterface $logger;

    public function __construct(OrganizationRepository $or, ContactCreationRepository $ccr, CacheItemPoolInterface $c, LoggerInterface $l)
    {
        $this->orgaRepo = $or;
        $this->contactCreationRepo = $ccr;
        $this->cache = $c;
        $this->logger = $l;
    }

    public function __invoke(RefreshContactStatsMessage $message)
    {
        // If the organization has been deleted, ignore messages
        if (!$orga = $this->orgaRepo->find($message->getOrganizationId())) {
            $this->logger->error('Organization not found.', ['organization_id' => $message->getOrganizationId()]);

            return;
        }

        // Compute stats maximum every 15 sec per organization
        $item = $this->cache->getItem('refresh-contacts-stats-'.$orga->getId());

        if ($item->isHit()) {
            $this->logger->info('Skipping already computed stats.', ['organization_id' => $message->getOrganizationId()]);

            return;
        }

        // Compute stats
        $statsComputed = $this->contactCreationRepo->refreshOrganizationStats($orga);

        // If stats refresh failed due to a lock on data, let the next refresh fix it
        // Otherwise, ignore next refresh requests for 15 seconds
        if ($statsComputed) {
            $item->set(true);
            $item->expiresAfter(15);

            $this->cache->save($item);
        }
    }
}
