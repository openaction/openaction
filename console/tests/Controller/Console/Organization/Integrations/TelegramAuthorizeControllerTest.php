<?php

namespace App\Tests\Controller\Console\Organization\Integrations;

use App\Repository\Integration\TelegramAppAuthorizationRepository;
use App\Tests\WebTestCase;

class TelegramAuthorizeControllerTest extends WebTestCase
{
    public function testAuthorizeAnonymous()
    {
        $client = static::createClient();
        $client->request('GET', '/console/i/t-me/1mart8wanRysFnJD3QjCS3');
        $this->assertResponseRedirects('/security/login');
    }

    public function testAuthorizeNo()
    {
        $client = static::createClient();
        $this->authenticate($client, 'adrien.duguet@citipo.com');

        $this->assertSame(1, self::getContainer()->get(TelegramAppAuthorizationRepository::class)->count([]));

        $client->request('GET', '/console/i/t-me/1mart8wanRysFnJD3QjCS3');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Do you want to share your account access with this bot?")');
        $this->assertSelectorExists('a:contains("Yes, authorize")');
        $this->assertSelectorExists('a:contains("No, cancel")');

        $client->clickLink('No, cancel');
        $this->assertSame(1, self::getContainer()->get(TelegramAppAuthorizationRepository::class)->count([]));
    }

    public function testAuthorizeYes()
    {
        $client = static::createClient();
        $this->authenticate($client, 'adrien.duguet@citipo.com');

        $this->assertSame(1, self::getContainer()->get(TelegramAppAuthorizationRepository::class)->count([]));

        $client->request('GET', '/console/i/t-me/1mart8wanRysFnJD3QjCS3');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Do you want to share your account access with this bot?")');
        $this->assertSelectorExists('a:contains("Yes, authorize")');
        $this->assertSelectorExists('a:contains("No, cancel")');

        $client->clickLink('Yes, authorize');
        $this->assertSame(2, self::getContainer()->get(TelegramAppAuthorizationRepository::class)->count([]));

        $authorization = self::getContainer()->get(TelegramAppAuthorizationRepository::class)->findOneBy([], ['id' => 'desc']);
        $this->assertSame('adrien.duguet@citipo.com', $authorization->getMember()->getEmail());
        $this->assertSame('citipodebugbot', $authorization->getApp()->getBotUsername());
        $this->assertResponseRedirects('https://telegram.me/citipodebugbot?start='.$authorization->getApiToken());
    }

    public function testAuthorizeAlready()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $this->assertSame(1, self::getContainer()->get(TelegramAppAuthorizationRepository::class)->count([]));

        $client->request('GET', '/console/i/t-me/1mart8wanRysFnJD3QjCS3');
        $this->assertResponseRedirects();
        $this->assertStringStartsWith('https://telegram.me/citipodebugbot?start=', $client->getResponse()->headers->get('Location'));
    }
}
