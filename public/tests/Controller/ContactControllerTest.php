<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testAccessManageGdpr()
    {
        $client = self::createClient();
        $client->request('GET', '/gdpr/3aKCEDnsBNA8PYe6xqkO9u');
        $this->assertResponseIsSuccessful();
    }
}
