<?php

namespace App\Client\PassKey;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TokenResolver
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly HttpClientInterface $citipo,

        #[Autowire('%env(APP_TOKEN_RESOLVER_KEY)%')]
        private readonly string $tokenResolverKey,
    ) {
    }

    public function resolveProjectToken(string $domain): ?string
    {
        $domain = strtolower($domain);

        return $this->cache->get($this->getCacheKey($domain), function (ItemInterface $item) use ($domain) {
            $item->expiresAfter(3600);

            $response = $this->citipo->request('GET', '/api/token-resolver/'.rawurlencode($domain), [
                'query' => ['key' => $this->tokenResolverKey],
            ]);

            if (Response::HTTP_FORBIDDEN === $response->getStatusCode()) {
                throw new \RuntimeException('Invalid APP_TOKEN_RESOLVER_KEY configured for token resolution.');
            }

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                return null;
            }

            $data = $response->toArray(false);

            return $data['token'] ?? null;
        });
    }

    private function getCacheKey(string $domain): string
    {
        return 'token-resolver-'.$domain;
    }
}
