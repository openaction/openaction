<?php

namespace App\Tests\Controller\Console\Organization\Integrations;

use App\Tests\WebTestCase;

class IntegromatControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/integrations/integromat');
        $this->assertResponseIsSuccessful();
    }
}
