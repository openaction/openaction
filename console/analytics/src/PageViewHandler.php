<?php

namespace Analytics;

use function donatj\UserAgent\parse_user_agent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageViewHandler
{
    private PageViewPersister $persister;

    public function __construct(PageViewPersister $persister)
    {
        $this->persister = $persister;
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->createEmptyResponse();
        }

        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception) {
            return $this->createEmptyResponse();
        }

        $eventName = $payload['n'] ?? null;

        if ('pageview' === $eventName) {
            $this->handlePageView($payload, $request);
        } elseif ('contentview' === $eventName) {
            $this->handleContentView($payload);
        } elseif ('customevent' === $eventName) {
            $this->handleCustomEvent($payload, $request);
        }

        return $this->createEmptyResponse();
    }

    private function handlePageView(array $payload, Request $request)
    {
        $projectId = $payload['p'] ?? null;
        $url = $payload['u'] ?? null;

        if (!$projectId || !$url) {
            return;
        }

        $country = strtolower($request->headers->get('CF-IPCountry', 'xx'));

        try {
            $details = parse_user_agent($request->headers->get('User-Agent'));
        } catch (\Exception) {
            $details = [];
        }

        $referrerParts = !empty($payload['r']) ? (array) parse_url($payload['r']) : [];

        $this->persister->persist(
            $this->decodeUuid($projectId),
            $request->headers->get('CF-Connecting-IP', '127.0.0.1'),
            substr(parse_url($url, PHP_URL_PATH) ?: '/', 0, 250),
            $details['platform'] ?? null,
            $details['browser'] ?? null,
            'xx' !== $country ? $country : null,
            !empty($referrerParts['host']) ? substr($referrerParts['host'], 0, 100) : null,
            !empty($referrerParts['path']) ? substr($referrerParts['path'], 0, 250) : null,
            $payload['uso'] ?? null,
            $payload['ume'] ?? null,
            $payload['uca'] ?? null,
            $payload['uco'] ?? null,
        );
    }

    private function handleContentView(array $payload)
    {
        $entityType = $payload['m']['type'] ?? null;
        $entityId = $payload['m']['id'] ?? null;

        if ($entityType && $entityId) {
            $this->persister->incrementPageViews($entityType, $this->decodeUuid($entityId));
        }
    }

    private function handleCustomEvent(array $payload, Request $request)
    {
        $projectId = $payload['p'] ?? null;
        $eventName = $payload['m']['event'] ?? null;

        if (!$projectId || !$eventName) {
            return;
        }

        $this->persister->persistEvent(
            $this->decodeUuid($projectId),
            $request->headers->get('CF-Connecting-IP', '127.0.0.1'),
            $eventName
        );
    }

    private function decodeUuid(string $encoded): string
    {
        return array_reduce(
            [20, 16, 12, 8],
            static function ($uuid, $offset) {
                return substr_replace($uuid, '-', $offset, 0);
            },
            str_pad(gmp_strval(gmp_init($encoded, 62), 16), 32, '0', STR_PAD_LEFT)
        );
    }

    private function createEmptyResponse(): Response
    {
        $response = new Response('');
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', '*');

        return $response;
    }
}
