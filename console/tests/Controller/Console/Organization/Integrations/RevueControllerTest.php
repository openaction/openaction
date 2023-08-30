<?php

namespace App\Tests\Controller\Console\Organization\Integrations;

use App\Entity\Integration\RevueAccount;
use App\Repository\Integration\RevueAccountRepository;
use App\Tests\WebTestCase;

class RevueControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/revue');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-block'));
    }

    public function testConnect()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/revue/connect');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'revue_account[label]' => 'accountlabel',
            'revue_account[apiToken]' => 'apitoken',
        ]);

        $this->assertResponseRedirects();

        $app = self::getContainer()->get(RevueAccountRepository::class)->findOneBy(['label' => 'accountlabel']);
        $this->assertInstanceOf(RevueAccount::class, $app);

        $this->assertSame('accountlabel', $app->getLabel());
        $this->assertSame('apitoken', $app->getApiToken());
        $this->assertNull($app->getLastSync());
        $this->assertTrue($app->isEnabled());
        $this->assertSame('Acme', $app->getOrganization()->getName());
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/revue/deae46c2-20df-4ba3-9c08-9bfc1d638f32/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'revue_account[label]' => 'accountlabel',
            'revue_account[apiToken]' => 'apitoken',
        ]);

        $this->assertResponseRedirects();

        $app = self::getContainer()->get(RevueAccountRepository::class)->findOneBy(['uuid' => 'deae46c2-20df-4ba3-9c08-9bfc1d638f32']);
        $this->assertInstanceOf(RevueAccount::class, $app);

        $this->assertSame('accountlabel', $app->getLabel());
        $this->assertSame('apitoken', $app->getApiToken());
        $this->assertNull($app->getLastSync());
        $this->assertTrue($app->isEnabled());
        $this->assertSame('Acme', $app->getOrganization()->getName());
    }

    public function testDisconnect()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/revue');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-block'));

        $client->click($crawler->selectLink('Disconnect')->link());
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.world-block'));
    }
}
