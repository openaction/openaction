<?php

namespace App\Tests\Controller\Console\Organization\Integrations;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;

// No HTTP mocking needed; MollieConnect is replaced by a test implementation

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

        // No HTTP calls in tests; service is overridden by MockMollieConnect

        // Start flow to get state
        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/mollie/connect');
        $this->assertResponseRedirects();
        $location = $client->getResponse()->headers->get('Location');
        parse_str(parse_url($location, PHP_URL_QUERY) ?: '', $params);
        $state = $params['state'];

        // Callback goes to Bridge fixed endpoint with state carrying org id
        $client->request('GET', '/bridge/mollie/connect/callback?code=thecode&state='.$state);
        $this->assertResponseRedirects('/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations');

        /** @var Organization $orga */
        $orga = self::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => 'cbeb774c-284c-43e3-923a-5a2388340f91']);
        $this->assertInstanceOf(Organization::class, $orga);
        $this->assertSame('access_123', $orga->getMollieConnectAccessToken());
        $this->assertSame('refresh_456', $orga->getMollieConnectRefreshToken());
    }
}
