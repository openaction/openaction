<?php

namespace App\Tests\Controller\Security;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends WebTestCase
{
    public function provideValidLoginCredentials()
    {
        yield ['titouan.galopin@citipo.com', 'password', 'Titouan Galopin'];
    }

    /**
     * @dataProvider provideValidLoginCredentials
     */
    public function testLoginValidCredentials(string $email, string $password, string $expectedName)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/security/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Log in')->form();

        $client->submit($form, ['email' => $email, 'password' => $password]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertTrue($client->getResponse()->isRedirect('/'));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('a:contains("Log out")'));
        $this->assertCount(1, $crawler->filter('[data-user]:contains("'.$expectedName.'")'));
    }

    public function provideInvalidLoginCredentials()
    {
        yield 'invalid_email' => ['invalid@citipo.com', 'password'];
        yield 'empty_email' => ['', 'password'];
        yield 'empty_password' => ['invalid@citipo.com', ''];
        yield 'invalid_password' => ['titouan.galopin@citipo.com', 'aaa'];
    }

    /**
     * @dataProvider provideInvalidLoginCredentials
     */
    public function testLoginInvalidCredentials(string $email, string $password)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/security/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Log in')->form();

        $client->submit($form, ['email' => $email, 'password' => $password]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertTrue($client->getResponse()->isRedirect('/security/login'));

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button:contains("Log in")'));
        $this->assertCount(1, $crawler->filter('.alert-danger'));
    }

    public function testLogout()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/security/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertTrue($client->getResponse()->isRedirect('/'));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $link = $crawler->filter('a:contains("Log out")'));
        $this->assertCount(1, $crawler->filter('[data-user]:contains("Titouan Galopin")'));

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/security/login'));

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
