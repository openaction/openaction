<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthControllerTest extends WebTestCase
{
    public function testHealthEndpointIsAlwaysOk(): void
    {
        $client = self::createClient();
        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        $this->assertSame('OK', $client->getResponse()->getContent());
        $this->assertSame('text/plain; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));
    }
}
