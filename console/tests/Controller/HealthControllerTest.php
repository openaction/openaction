<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class HealthControllerTest extends WebTestCase
{
    public function testHealth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');
        $this->assertResponseIsSuccessful();
    }
}
