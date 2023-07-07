<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class EventCategoryControllerTest extends ApiTestCase
{
    public function testList()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/events-categories');
        $this->assertCount(2, $result['data']);

        // Test mapping and weight
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'EventCategory',
                    '_links' => [
                        'self' => 'http://localhost/api/website/events-categories/1r1BjY3gmpXo6e4KH7kMNU',
                    ],
                    'id' => '1r1BjY3gmpXo6e4KH7kMNU',
                    'name' => 'Category 1',
                    'slug' => 'category-1',
                ],
                [
                    '_resource' => 'EventCategory',
                    '_links' => [
                        'self' => 'http://localhost/api/website/events-categories/1RBufbuXcErYt5XplZHhc',
                    ],
                    'id' => '1RBufbuXcErYt5XplZHhc',
                    'name' => 'Category 2',
                    'slug' => 'category-2',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/events-categories', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/events-categories', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/events-categories/1RBufbuXcErYt5XplZHhc');
        $this->assertApiResponse($result, [
            '_resource' => 'EventCategory',
            '_links' => [
                'self' => 'http://localhost/api/website/events-categories/1RBufbuXcErYt5XplZHhc',
            ],
            'id' => '1RBufbuXcErYt5XplZHhc',
            'name' => 'Category 2',
            'slug' => 'category-2',
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/events-categories/1RBufbuXcErYt5XplZHhc', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/events-categories/1RBufbuXcErYt5XplZHhc', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/events-categories/invalid', self::DEFAULT_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/events-categories/7rnedzqzqk0hv5ktdm3a1m', self::DEFAULT_TOKEN, 404);
    }
}
