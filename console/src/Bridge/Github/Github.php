<?php

namespace App\Bridge\Github;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Github implements GithubInterface
{
    private HttpClientInterface $httpClient;
    private CacheInterface $cache;

    private string $appId;
    private string $appKey;

    public function __construct(HttpClientInterface $h, CacheInterface $c, string $appId, string $appKey)
    {
        $this->httpClient = $h;
        $this->cache = $c;
        $this->appId = $appId;
        $this->appKey = $appKey;
    }

    public function getFileContent(string $installationId, string $repository, string $pathname): ?string
    {
        $response = $this->httpClient->request('GET', 'https://api.github.com/repos/'.$repository.'/contents/'.$pathname, [
            'headers' => [
                'Authorization' => 'Token '.$this->getInstallationToken($installationId),
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return base64_decode($response->toArray()['content']);
    }

    private function getInstallationToken(string $installationId): string
    {
        return $this->cache->get(
            'github-installation-token-'.$installationId,
            \Closure::fromCallable([$this, 'createInstallationToken'])->bindTo($this)
        );
    }

    private function createInstallationToken(ItemInterface $item): string
    {
        $installationId = str_replace('github-installation-token-', '', $item->getKey());

        $response = $this->httpClient->request('POST', 'https://api.github.com/app/installations/'.$installationId.'/access_tokens', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getAppJwt(),
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ]);

        try {
            $data = $response->toArray();
        } catch (ClientException) {
            throw new \InvalidArgumentException(sprintf('Invalid GitHub credentials, access token generation failed: HTTP %s (%s)', $response->getStatusCode(), $response->getContent(false)));
        }

        $item->expiresAt(new \DateTime($data['expires_at']));

        return $data['token'];
    }

    private function getAppJwt(): string
    {
        return $this->cache->get('github-jwt', \Closure::fromCallable([$this, 'createJwt'])->bindTo($this));
    }

    private function createJwt(ItemInterface $item): string
    {
        $item->expiresAfter(5 * 60);

        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($this->appKey));
        $now = new \DateTimeImmutable();

        return $config->builder(ChainedFormatter::withUnixTimestampDates())
            ->issuedBy($this->appId)
            ->issuedAt($now)
            ->expiresAt($now->modify('+5 minutes'))
            ->getToken($config->signer(), $config->signingKey())
            ->toString()
        ;
    }
}
