<?php

namespace App\Bridge\Revue;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Revue implements RevueInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getSubscribers(string $apiToken): array
    {
        $response = $this->httpClient->request('GET', 'https://www.getrevue.co/api/v2/subscribers', [
            'headers' => [
                'Authorization' => 'Token '.$apiToken,
            ],
        ]);

        if (401 === $response->getStatusCode()) {
            throw new \InvalidArgumentException('Invalid API token.');
        }

        return $response->toArray();
    }
}
