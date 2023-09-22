<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class TrombinoscopeCategoryControllerTest extends ApiTestCase
{
    public function testList()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope-categories', self::ACME_TOKEN);
        $this->assertCount(2, $result['data']);

        // Test mapping and weight
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'TrombinoscopeCategory',
                    '_links' => [
                        'self' => 'http://localhost/api/website/trombinoscope-categories/7Fy7Er4zVNHhotuL9q2JfQ',
                    ],
                    'id' => '7Fy7Er4zVNHhotuL9q2JfQ',
                    'name' => 'Loire-Atlantique',
                    'slug' => 'loire-atlantique',
                ],
                [
                    '_resource' => 'TrombinoscopeCategory',
                    '_links' => [
                        'self' => 'http://localhost/api/website/trombinoscope-categories/2K2eIimDSzPWUGNrYy7xmg',
                    ],
                    'id' => '2K2eIimDSzPWUGNrYy7xmg',
                    'name' => 'Eure-et-Loir',
                    'slug' => 'eure-et-loir',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope-categories', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope-categories', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope-categories/7Fy7Er4zVNHhotuL9q2JfQ', self::ACME_TOKEN);
        $this->assertApiResponse($result, [
            '_resource' => 'TrombinoscopeCategory',
            '_links' => [
                'self' => 'http://localhost/api/website/trombinoscope-categories/7Fy7Er4zVNHhotuL9q2JfQ',
            ],
            'id' => '7Fy7Er4zVNHhotuL9q2JfQ',
            'name' => 'Loire-Atlantique',
            'slug' => 'loire-atlantique',
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope-categories/7Fy7Er4zVNHhotuL9q2JfQ', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope-categories/7Fy7Er4zVNHhotuL9q2JfQ', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/trombinoscope-categories/invalid', self::EXAMPLECO_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/trombinoscope-categories/7rnedzqzqk0hv5ktdm3a1m', self::EXAMPLECO_TOKEN, 404);
    }
}
