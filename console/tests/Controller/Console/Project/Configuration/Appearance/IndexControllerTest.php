<?php

namespace App\Tests\Controller\Console\Project\Configuration\Appearance;

use App\Tests\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance');
        $this->assertResponseIsSuccessful();
    }
}
