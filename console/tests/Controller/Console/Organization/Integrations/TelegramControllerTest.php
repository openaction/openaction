<?php

namespace App\Tests\Controller\Console\Organization\Integrations;

use App\Entity\Integration\TelegramApp;
use App\Repository\Integration\TelegramAppRepository;
use App\Tests\WebTestCase;

class TelegramControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/telegram');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.world-block'));
    }

    public function testDetails()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/telegram/3a9c0c55-bb74-48d7-9cce-117fbf8e0293/details');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[value="http://localhost/console/i/t-me/1mart8wanRysFnJD3QjCS3"]');
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/telegram');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.world-block'));

        $client->click($crawler->selectLink('Delete')->link());
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $crawler->filter('.world-block'));
    }

    public function testRegister()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/telegram/register');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Register')->form();
        $client->submit($form, [
            'telegram_app[botUsername]' => 'botusername',
        ]);

        $this->assertResponseRedirects();

        $app = self::getContainer()->get(TelegramAppRepository::class)->findOneBy(['botUsername' => 'botusername']);
        $this->assertInstanceOf(TelegramApp::class, $app);

        $this->assertSame('botusername', $app->getBotUsername());
        $this->assertSame('Acme', $app->getOrganization()->getName());
    }
}
