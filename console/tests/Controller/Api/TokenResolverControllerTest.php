<?php

namespace App\Tests\Controller\Api;

use App\Tests\ApiRequestBuilder;
use App\Tests\ApiTestCase;
use App\Util\Json;

class TokenResolverControllerTest extends ApiTestCase
{
    public function testResolveWithValidKey(): void
    {
        $response = $this->createApiRequest('GET', '/api/token-resolver/exampleco.com')
            ->withApiToken(null)
            ->withParameters(['key' => $_ENV['APP_TOKEN_RESOLVER_KEY']])
            ->send();

        $this->assertResponseIsSuccessful();
        $this->assertSame(['token' => ApiRequestBuilder::TOKEN_DEFAULT], Json::decode($response->getContent()));
    }

    public function testResolveWithInvalidKey(): void
    {
        $this->createApiRequest('GET', '/api/token-resolver/exampleco.com')
            ->withApiToken(null)
            ->withParameters(['key' => 'invalid'])
            ->send();

        $this->assertResponseStatusCodeSame(403);
    }

    public function testResolveWithUnknownHostname(): void
    {
        $this->createApiRequest('GET', '/api/token-resolver/unknown.example.com')
            ->withApiToken(null)
            ->withParameters(['key' => $_ENV['APP_TOKEN_RESOLVER_KEY']])
            ->send();

        $this->assertResponseStatusCodeSame(404);
    }
}
