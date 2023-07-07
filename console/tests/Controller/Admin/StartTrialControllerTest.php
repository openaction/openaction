<?php

namespace App\Tests\Controller\Admin;

use App\Tests\WebTestCase;

class StartTrialControllerTest extends WebTestCase
{
    public function testStartTrial()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Empty organization');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Create the organization');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'start_trial[name]' => 'Trial Organization',
        ]);
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('.list-group-item:contains("Trial Organization")'));
    }
}
