<?php

namespace App\Tests\Controller\Bridge;

use App\Entity\Integration\IntegromatWebhook;
use App\Repository\Integration\IntegromatWebhookRepository;
use App\Tests\WebTestCase;
use App\Util\Json;

class IntegromatControllerTest extends WebTestCase
{
    public function testIndexValid()
    {
        $client = static::createClient();
        $client->request('POST', '/webhook/integromat', [], [], ['HTTP_X_API_KEY' => '645b5d9c2e3ac8064c540b276b6c180692582868cd21c6b50e4442267e5a341f']);
        $this->assertResponseIsSuccessful();
        $this->assertJson($json = $client->getResponse()->getContent());
        $this->assertSame(['organization' => 'Citipo'], Json::decode($json));
    }

    public function testIndexInvalid()
    {
        $client = static::createClient();
        $client->request('POST', '/webhook/integromat', [], [], ['HTTP_X_API_KEY' => 'invalid']);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testAttach()
    {
        $client = static::createClient();

        $client->request('POST',
            '/webhook/integromat/attach',
            [],
            [],
            ['HTTP_X_API_KEY' => '645b5d9c2e3ac8064c540b276b6c180692582868cd21c6b50e4442267e5a341f'],
            Json::encode(['url' => 'https://example.com'])
        );
        $this->assertResponseStatusCodeSame(200);

        /** @var IntegromatWebhook $webhook */
        $webhook = static::getContainer()->get(IntegromatWebhookRepository::class)->findOneBy([
            'integromatUrl' => 'https://example.com',
        ]);

        $this->assertInstanceOf(IntegromatWebhook::class, $webhook);
        $this->assertSame('Citipo', $webhook->getOrganization()->getName());
        $this->assertNotEmpty($webhook->getIntegromatUrl());
    }

    public function testDetach()
    {
        $client = static::createClient();

        /** @var IntegromatWebhook $webhook */
        $webhook = static::getContainer()->get(IntegromatWebhookRepository::class)->findOneBy([
            'token' => 'adfdb95e90476cf7628eb8cd5c739cde6307b0e505daa60e2541c245adab86ef',
        ]);

        $this->assertInstanceOf(IntegromatWebhook::class, $webhook);

        $client->request('POST',
            '/webhook/integromat/detach/adfdb95e90476cf7628eb8cd5c739cde6307b0e505daa60e2541c245adab86ef',
            [],
            [],
            ['HTTP_X_API_KEY' => '645b5d9c2e3ac8064c540b276b6c180692582868cd21c6b50e4442267e5a341f']
        );
        $this->assertResponseStatusCodeSame(200);

        /** @var IntegromatWebhook $webhook */
        $webhook = static::getContainer()->get(IntegromatWebhookRepository::class)->findOneBy([
            'token' => 'adfdb95e90476cf7628eb8cd5c739cde6307b0e505daa60e2541c245adab86ef',
        ]);

        $this->assertNull($webhook);
    }
}
