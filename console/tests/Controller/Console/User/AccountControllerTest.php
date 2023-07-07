<?php

namespace App\Tests\Controller\Console\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\WebTestCase;

class AccountControllerTest extends WebTestCase
{
    public function testUpdate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/user/account');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('input[type=text]'));

        $button = $crawler->selectButton('Update');
        $client->submit($button->form(), [
            'account[firstName]' => 'First name',
            'account[lastName]' => 'Last name',
            'account[locale]' => 'fr',
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => self::USER_TGALOPIN_EMAIL]);
        $this->assertEquals('First name', $user->getFirstName());
        $this->assertEquals('Last name', $user->getLastName());
        $this->assertEquals('fr', $user->getLocale());
    }
}
