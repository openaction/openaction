<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Tests\WebTestCase;

class PetitionControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();

        // Should list petitions from fixtures
        $this->assertCount(1, $crawler->filter('.world-list-row'));
        $this->assertStringContainsString('Save the Park', $crawler->filter('.petitions-title')->text());
    }
}

