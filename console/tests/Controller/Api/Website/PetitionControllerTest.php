<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class PetitionControllerTest extends ApiTestCase
{
    public function testList()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/petitions', self::ACME_TOKEN);

        // One petition from fixtures
        $this->assertCount(1, $result['data']);

        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'Petition',
                    'id' => 'save-the-park',
                    'slug' => 'save-the-park',
                ],
            ],
        ]);

        // Ensure content and form are not included in list localizations
        $this->assertArrayHasKey('localizations', $result['data'][0]);
        $this->assertArrayHasKey('data', $result['data'][0]['localizations']);
        $this->assertIsArray($result['data'][0]['localizations']['data']);
        $firstLoc = $result['data'][0]['localizations']['data'][0];
        $this->assertArrayNotHasKey('content', $firstLoc);
        $this->assertArrayNotHasKey('form', $firstLoc);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/petitions', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/petitions', 'invalid', 401);
    }

    public function testViewBySlug()
    {
        $client = self::createClient();
        $result = $this->apiRequest($client, 'GET', '/api/website/petitions/save-the-park', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            '_resource' => 'Petition',
            'id' => 'save-the-park',
            'slug' => 'save-the-park',
        ]);

        // Ensure localizations include content and form keys inline
        $this->assertArrayHasKey('localizations', $result);
        $this->assertArrayHasKey('data', $result['localizations']);
        $loc = $result['localizations']['data'][0];
        $this->assertArrayHasKey('content', $loc);
        $this->assertTrue(array_key_exists('form', $loc)); // can be null

        // Categories embedded under localization
        $this->assertArrayHasKey('categories', $loc);
        $this->assertArrayHasKey('data', $loc['categories']);
        $this->assertGreaterThanOrEqual(1, count($loc['categories']['data']));
        $this->assertArrayHasKey('name', $loc['categories']['data'][0]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/petitions/save-the-park', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/petitions/save-the-park', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();
        $this->apiRequest($client, 'GET', '/api/website/petitions/does-not-exist', self::ACME_TOKEN, 404);
    }
}
