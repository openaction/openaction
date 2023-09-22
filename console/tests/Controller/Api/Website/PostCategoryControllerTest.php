<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class PostCategoryControllerTest extends ApiTestCase
{
    public function testList()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/posts-categories');
        $this->assertCount(2, $result['data']);

        // Test mapping and weight
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'PostCategory',
                    '_links' => [
                        'self' => 'http://localhost/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1',
                    ],
                    'id' => '1GmkaorS3YSezgfKGrZel1',
                    'name' => 'Category 2',
                    'slug' => 'category-2',
                ],
                [
                    '_resource' => 'PostCategory',
                    '_links' => [
                        'self' => 'http://localhost/api/website/posts-categories/6Xtiq0UNncsemt50yvqsoj',
                    ],
                    'id' => '6Xtiq0UNncsemt50yvqsoj',
                    'name' => 'Category 1',
                    'slug' => 'category-1',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/posts-categories', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/posts-categories', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1');
        $this->assertApiResponse($result, [
            '_resource' => 'PostCategory',
            '_links' => [
                'self' => 'http://localhost/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1',
            ],
            'id' => '1GmkaorS3YSezgfKGrZel1',
            'name' => 'Category 2',
            'slug' => 'category-2',
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/posts-categories/invalid', self::EXAMPLECO_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/posts-categories/7rnedzqzqk0hv5ktdm3a1m', self::EXAMPLECO_TOKEN, 404);
    }
}
