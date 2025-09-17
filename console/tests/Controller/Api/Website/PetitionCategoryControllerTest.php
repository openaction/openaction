<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class PetitionCategoryControllerTest extends ApiTestCase
{
    public function testList()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/petitions-categories', self::ACME_TOKEN);

        $this->assertCount(2, $result['data']);

        $names = array_map(static fn ($c) => $c['name'], $result['data']);
        sort($names);
        $this->assertSame(['General', 'Policy'], $names);

        // Spot check mapping
        $this->assertArrayHasKey('slug', $result['data'][0]);
        $this->assertArrayHasKey('_links', $result['data'][0]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/petitions-categories', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/petitions-categories', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        // UUID base62 for 'General' fixture: f9b7bd2a-9e6e-49d4-9f3c-5f2f20b2f901 -> compute from repository in runtime is not possible here, so leverage listing
        $list = $this->apiRequest($client, 'GET', '/api/website/petitions-categories', self::ACME_TOKEN);
        $id = $list['data'][0]['id'];
        $result = $this->apiRequest($client, 'GET', '/api/website/petitions-categories/'.$id, self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            '_resource' => 'PetitionCategory',
            'id' => $id,
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/petitions-categories/7hIQY74GJcZWKsJxafwbHZ', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/petitions-categories/7hIQY74GJcZWKsJxafwbHZ', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();
        $this->apiRequest($client, 'GET', '/api/website/petitions-categories/invalid', self::EXAMPLECO_TOKEN, 404);
    }
}
