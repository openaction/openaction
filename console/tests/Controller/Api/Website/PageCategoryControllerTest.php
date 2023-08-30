<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class PageCategoryControllerTest extends ApiTestCase
{
    public function testList()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/pages-categories');
        $this->assertCount(2, $result['data']);

        // Test mapping and weight
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'PageCategory',
                    '_links' => [
                        'self' => 'http://localhost/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ',
                    ],
                    'id' => '7hIQY74GJcZWKsJxafwbHZ',
                    'name' => 'Category 2',
                    'slug' => 'category-2',
                ],
                [
                    '_resource' => 'PageCategory',
                    '_links' => [
                        'self' => 'http://localhost/api/website/pages-categories/2j7qdd4EDE0CJVOw0WCWAp',
                    ],
                    'id' => '2j7qdd4EDE0CJVOw0WCWAp',
                    'name' => 'Category 1',
                    'slug' => 'category-1',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/pages-categories', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/pages-categories', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ');

        $this->assertApiResponse($result, [
            '_resource' => 'PageCategory',
            '_links' => [
                'self' => 'http://localhost/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ',
            ],
            'id' => '7hIQY74GJcZWKsJxafwbHZ',
            'name' => 'Category 2',
            'slug' => 'category-2',
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/pages-categories/invalid', self::DEFAULT_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/pages-categories/7rnedzqzqk0hv5ktdm3a1m', self::DEFAULT_TOKEN, 404);
    }
}
