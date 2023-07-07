<?php

namespace App\Tests\Controller\Admin;

use App\Tests\WebTestCase;

class SmokeTestControllerTest extends WebTestCase
{
    public function provideAdminPages()
    {
        yield ['Announcements'];
        yield ['Billing dashboard'];
        yield ['Orders'];
        yield ['Quotes'];
        yield ['Organizations'];
        yield ['Domains'];
        yield ['Projects'];
        yield ['Users'];
        yield ['Registrations'];
        yield ['Print dashboard'];
        yield ['Ordered'];
        yield ['Drafts'];
    }

    /**
     * @dataProvider provideAdminPages
     */
    public function testAdminPages(string $linkText)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        $client->clickLink($linkText);
        $this->assertResponseIsSuccessful();
    }
}
