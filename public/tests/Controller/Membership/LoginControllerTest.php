<?php

namespace App\Tests\Controller\Membership;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Cookie;

class LoginControllerTest extends WebTestCase
{
    public function testValid()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/members/login');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Se connecter');
        $client->submit($button->form(), [
            'login[email]' => 'jeanpaul@gmail.com',
            'login[password]' => 'password',
        ]);

        $this->assertResponseRedirects('/members/area/dashboard');

        $cookies = $client->getResponse()->headers->getCookies();
        $this->assertCount(1, $cookies);

        /** @var Cookie $cookie */
        $cookie = $cookies[0];
        $this->assertSame('citipo_auth_token', $cookie->getName());

        $payload = json_decode($cookie->getValue(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Jean', $payload['firstName']);
        $this->assertSame('Paul', $payload['lastName']);
        $this->assertArrayHasKey('nonce', $payload);
        $this->assertArrayHasKey('encrypted', $payload);
    }

    public function testInvalid()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/members/login');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Se connecter');
        $client->submit($button->form(), [
            'login[email]' => 'jeanpaul@gmail.com',
            'login[password]' => 'invalid',
        ]);

        $this->assertResponseRedirects('/members/login?error=credentials');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $client->getResponse()->headers->getCookies());
    }
}
