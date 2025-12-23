<?php

namespace App\Proxy;

use App\Repository\ProjectRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DomainTokenCache
{
    private CacheInterface $cache;
    private ProjectRepository $repository;

    public function __construct(CacheInterface $cache, ProjectRepository $repository)
    {
        $this->cache = $cache;
        $this->repository = $repository;
    }

    /**
     * Refreshes the shared cache used by public to choose the project
     * token to use for a given domain.
     */
    public function refresh(): void
    {
        $this->cache->get('domains-tokens', function (ItemInterface $item) {
            $item->expiresAfter(null);

            return $this->repository->findDomainsTokens();
        });
    }
}
