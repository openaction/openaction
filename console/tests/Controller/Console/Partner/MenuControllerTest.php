<?php

namespace App\Tests\Controller\Console\Partner;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\WebTestCase;

class MenuControllerTest extends WebTestCase
{
    public function testMenu()
    {
        $client = static::createClient();
        $this->authenticate($client, 'adrien.duguet@citipo.com');

        $crawler = $client->request('GET', '/console/partner/menu');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $client->submit($button->form(), [
            'partner_menu[label1]' => 'Label 1',
            'partner_menu[url1]' => 'https://google.com/1',
            'partner_menu[label2]' => 'Label 2',
            'partner_menu[url2]' => 'https://google.com/2',
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'adrien.duguet@citipo.com']);
        $this->assertSame(
            [
                'items' => [
                    ['label' => 'Label 1', 'url' => 'https://google.com/1'],
                    ['label' => 'Label 2', 'url' => 'https://google.com/2'],
                ],
            ],
            $user->getPartnerMenu()->toArray()
        );
    }
}
