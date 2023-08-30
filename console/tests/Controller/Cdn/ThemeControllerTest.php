<?php

namespace App\Tests\Controller\Cdn;

use App\Tests\WebTestCase;

class ThemeControllerTest extends WebTestCase
{
    public function testCss()
    {
        $client = static::createClient();
        $client->request('GET', '/theme/2c720420-65fd-4360-9d77-731758008497.css');
        $this->assertResponseIsSuccessful();
    }

    public function testJs()
    {
        $client = static::createClient();
        $client->request('GET', '/theme/2c720420-65fd-4360-9d77-731758008497.js');
        $this->assertResponseIsSuccessful();
    }

    public function testAsset()
    {
        $client = static::createClient();

        $fileContent = file_get_contents(__DIR__.'/../../Fixtures/upload/mario.png');

        // Create CDN file
        static::getContainer()->get('cdn.storage')->write('asset.png', $fileContent);

        // Try to access the content
        $client->request('GET', '/theme/asset/asset.png');

        $this->assertResponseIsSuccessful();
    }
}
