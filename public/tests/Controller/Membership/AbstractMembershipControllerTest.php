<?php

namespace App\Tests\Controller\Membership;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMembershipControllerTest extends WebTestCase
{
    protected function authenticate(KernelBrowser $client, string $email = 'jeanpaul@gmail.com', string $password = 'password')
    {
        $crawler = $client->request('GET', '/members/login');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Se connecter');
        $client->submit($button->form(), [
            'login[email]' => $email,
            'login[password]' => $password,
        ]);
        $this->assertResponseRedirects('/members/area/dashboard');

        $client->followRedirect();
    }
}
