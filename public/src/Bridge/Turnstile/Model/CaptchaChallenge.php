<?php

namespace App\Bridge\Turnstile\Model;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CaptchaChallenge
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $siteKey,
        private readonly string $secretKey,
    ) {
    }

    public function isValidResponse(?string $response): bool
    {
        if (!$response) {
            return false;
        }

        $httpResponse = $this->httpClient->request('POST', 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'body' => [
                'secret' => $this->secretKey,
                'response' => $response,
            ],
        ]);

        try {
            $success = $httpResponse->toArray()['success'] ?? false;
        } catch (\Throwable) {
            $success = false;
        }

        return $success;
    }

    public function getSiteKey(): string
    {
        return $this->siteKey;
    }
}
