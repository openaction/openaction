<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class DemoControllerTest extends WebTestCase
{
    public function testThemeServe()
    {
        $client = static::createClient();

        $client->request('GET', '/demo-login/OTg1YzI4ZTItZWYyZi');
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('.list-group-item:contains("Expired")'));
    }
}
