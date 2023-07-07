<?php

namespace App\Client\PassKey;

use Symfony\Contracts\Cache\CacheInterface;

class TokenResolver
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function resolveProjectToken(string $domain): ?string
    {
        return $this->cache->get('domains-tokens', fn () => [])[$domain] ?? null;
    }
}
