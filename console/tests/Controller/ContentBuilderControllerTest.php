<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class ContentBuilderControllerTest extends WebTestCase
{
    public function testWebBlocks()
    {
        $client = static::createClient();
        $client->request('GET', '/contentbuilder/assets/minimalist-blocks/content.js');
        $this->assertResponseIsSuccessful();
    }

    public function testEmailBlocks()
    {
        $client = static::createClient();
        $client->request('GET', '/contentbuilder/assets/email-blocks/content.js');
        $this->assertResponseIsSuccessful();
    }
}
