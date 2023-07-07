<?php

namespace App\Tests\Controller\Membership;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JoinControllerTest extends WebTestCase
{
    public function provideJoin()
    {
        yield [
            [
                'join[email]' => 'abc@def.com',
                'join[password][first]' => '12345678',
                'join[password][second]' => '12345678',
                'join[profileFirstName]' => 'profileFirstName',
                'join[profileLastName]' => 'profileLastName',
                'join[profileBirthdate][day]' => '1',
                'join[profileBirthdate][month]' => '1',
                'join[profileBirthdate][year]' => '1980',
                'join[contactPhone]' => '07 57 59 46 21',
                'join[contactWorkPhone]' => '07 57 59 46 22',
                'join[addressStreetLine1]' => 'addressStreetLine1',
                'join[addressStreetLine2]' => 'addressStreetLine2',
                'join[addressZipCode]' => '92110',
                'join[addressCity]' => 'Clichy',
                'join[addressCountry]' => 'FR',
                'join[settingsReceiveNewsletters]' => true,
                'join[settingsReceiveSms]' => true,
                'join[acceptPolicy]' => true,
            ],
        ];
    }

    /**
     * @dataProvider provideJoin
     */
    public function testJoin(array $data)
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/members/join');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Envoyer');
        $client->submit($button->form(), $data);

        $this->assertResponseRedirects();
    }
}
