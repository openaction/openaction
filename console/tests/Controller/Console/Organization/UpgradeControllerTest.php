<?php

namespace App\Tests\Controller\Console\Organization;

use App\Tests\WebTestCase;

class UpgradeControllerTest extends WebTestCase
{
    public function testUpgrade()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/upgrade');
        $this->assertResponseIsSuccessful();
    }
}
