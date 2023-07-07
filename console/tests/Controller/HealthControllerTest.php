<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class HealthControllerTest extends WebTestCase
{
    public function testHealth()
    {
        $client = static::createClient();

        $client->request('GET', '/health/G7PjZtNL7zZenQY23OoCax2Ng0bV8cvl');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Database: OK', $client->getResponse()->getContent());
        $this->assertStringContainsString('Redis: OK', $client->getResponse()->getContent());
    }
}
