<?php

namespace App\Feed;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EnclosureMetadataProvider
{
    private const CACHE_TTL = 3600;

    private array $runtimeCache = [];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function get(string $uri): ?array
    {
        if (array_key_exists($uri, $this->runtimeCache)) {
            return $this->runtimeCache[$uri];
        }

        return $this->runtimeCache[$uri] = $this->cache->get($this->getCacheKey($uri), function (ItemInterface $item) use ($uri) {
            $item->expiresAfter(self::CACHE_TTL);

            try {
                $response = $this->httpClient->request('HEAD', $uri, ['max_redirects' => 1]);
                if ($response->getStatusCode() >= 400) {
                    return null;
                }

                $headers = array_change_key_case($response->getHeaders(false), CASE_LOWER);
                $lengthHeader = $headers['content-length'][0] ?? null;
                $typeHeader = $headers['content-type'][0] ?? null;
            } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $exception) {
                $this->logger?->debug('Failed to resolve enclosure metadata', [
                    'uri' => $uri,
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);

                return null;
            }

            $length = $this->parseLength($lengthHeader);
            if (null === $length) {
                return null;
            }

            return [
                'uri' => $uri,
                'length' => $length,
                'type' => $this->parseType($typeHeader, $uri),
            ];
        });
    }

    private function parseLength(?string $value): ?int
    {
        if (!$value || !is_numeric($value)) {
            return null;
        }

        $length = (int) $value;

        return $length > 0 ? $length : null;
    }

    private function parseType(?string $header, string $uri): string
    {
        if ($header) {
            $type = trim(strtolower(strtok($header, ';')));
            if ($type) {
                return $type;
            }
        }

        if ($guessed = MimeTypes::getDefault()->guessMimeType($uri)) {
            return $guessed;
        }

        return 'image/jpeg';
    }

    private function getCacheKey(string $uri): string
    {
        return 'rss-enclosure-'.sha1($uri);
    }
}
