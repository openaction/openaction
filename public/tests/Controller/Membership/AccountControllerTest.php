<?php

namespace App\Tests\Controller\Membership;

class AccountControllerTest extends AbstractMembershipControllerTest
{
    public function provideAccount()
    {
        yield [
            [
                'update_account[profileFirstName]' => 'Jean',
                'update_account[profileLastName]' => 'Paul',
                'update_account[profileBirthdate][day]' => '1',
                'update_account[profileBirthdate][month]' => '1',
                'update_account[profileBirthdate][year]' => '1980',
                'update_account[contactPhone]' => '07 57 59 46 21',
                'update_account[contactWorkPhone]' => '07 57 59 46 22',
                'update_account[addressStreetLine1]' => 'addressStreetLine1',
                'update_account[addressStreetLine2]' => 'addressStreetLine2',
                'update_account[addressZipCode]' => '70070120',
                'update_account[addressCity]' => 'addressCity',
                'update_account[addressCountry]' => 'BR',
                'update_account[settingsReceiveNewsletters]' => true,
                'update_account[settingsReceiveSms]' => true,
            ],
        ];
    }

    /**
     * @dataProvider provideAccount
     */
    public function testAccount(array $data)
    {
        $client = self::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/members/area/account');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Enregistrer');
        $client->submit($button->form(), $data);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testUpdateEmail()
    {
        $client = self::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/members/area/account/update-email');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Enregistrer');
        $client->submit($button->form(), [
            'update_email[email]' => 'jeanpaul+2@gmail.com',
        ]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testUnregister()
    {
        $client = self::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/members/area/account/unregister');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Me désinscrire');
        $client->submit($button->form(), []);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
