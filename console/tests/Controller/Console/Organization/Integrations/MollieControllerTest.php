<?php

namespace App\Tests\Controller\Console\Organization\Integrations;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MollieControllerTest extends WebTestCase
{
    public function testConnectRedirectsToMollie()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/mollie/connect');
        $this->assertResponseRedirects();

        $location = $client->getResponse()->headers->get('Location');
        $this->assertNotNull($location);
        $this->assertStringContainsString('https://my.mollie.com/oauth2/authorize', $location);
        parse_str(parse_url($location, PHP_URL_QUERY) ?: '', $params);
        $this->assertSame(getenv('MOLLIE_CONNECT_CLIENT_ID') ?: $_ENV['MOLLIE_CONNECT_CLIENT_ID'], $params['client_id'] ?? null);
        $this->assertSame('code', $params['response_type'] ?? null);
        $this->assertNotEmpty($params['state'] ?? null);
        $this->assertStringContainsString('payments.write', $params['scope'] ?? '');
    }

    public function testCallbackStoresTokens()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Mock Mollie token response
        $mockResponse = new MockResponse(json_encode([
            'access_token' => 'access_123',
            'refresh_token' => 'refresh_456',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'scope' => 'payments.read organizations.read',
        ]), [
            'http_code' => 200,
            'response_headers' => ['content-type' => 'application/json'],
        ]);
        $mockClient = new MockHttpClient([$mockResponse]);

        static::getContainer()->set(HttpClientInterface::class, $mockClient);

        // Start flow to get state
        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/mollie/connect');
        $this->assertResponseRedirects();
        $location = $client->getResponse()->headers->get('Location');
        parse_str(parse_url($location, PHP_URL_QUERY) ?: '', $params);
        $state = $params['state'];

        // Callback
        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/mollie/callback?code=thecode&state='.$state);
        $this->assertResponseRedirects('/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations');

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => 'cbeb774c-284c-43e3-923a-5a2388340f91']);
        $this->assertInstanceOf(Organization::class, $orga);
        $this->assertSame('access_123', $orga->getMollieConnectAccessToken());
        $this->assertSame('refresh_456', $orga->getMollieConnectRefreshToken());
    }
}

