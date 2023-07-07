<?php

namespace App\Tests\Controller\Console\Partner;

use App\Tests\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testIndexAdmin()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/partner/dashboard');
        $this->assertResponseIsSuccessful();
        $this->assertSame(8, $crawler->filter('.world-block')->count());
    }

    public function testIndexPartner()
    {
        $client = static::createClient();
        $this->authenticate($client, 'adrien.duguet@citipo.com');

        $crawler = $client->request('GET', '/console/partner/dashboard');
        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $crawler->filter('.world-block')->count());
    }
}
