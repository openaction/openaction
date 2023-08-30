<?php

namespace App\Tests\Controller\Console\Organization\Integrations;

use App\Tests\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations');
        $this->assertResponseIsSuccessful();
    }
}
