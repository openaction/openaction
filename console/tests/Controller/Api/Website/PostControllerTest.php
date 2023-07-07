<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class PostControllerTest extends ApiTestCase
{
    public function testListAllPosts()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/posts');
        $this->assertCount(12, $result['data']);

        // Test content is not included in the payload
        $this->assertArrayNotHasKey('content', $result['data'][0]);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Test full mapping
                [
                    '_resource' => 'Post',
                    '_links' => [
                        'self' => 'http://localhost/api/website/posts/2m4VBvTA1NbUi7acpk7JFy',
                    ],
                    'id' => '2m4VBvTA1NbUi7acpk7JFy',
                    'title' => 'The EU must stand with the people of Hong Kong against China’s abuse of power',
                    'quote' => 'Quote 1',
                    'slug' => 'the-eu-must-stand-with-the-people-of-hong-kong-against-china-s-abuse-of-power',
                    'description' => 'Description 1',
                    'video' => 'youtube:nxaOzonmeic',
                    'image' => 'http://localhost/serve/post-image.png',
                    'sharer' => 'http://localhost/serve/post-image.png?t=sharer',
                    'categories' => [
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
                        ],
                    ],
                ],

                // Test nullable image, empty categories
                [
                    'id' => '5dekUWIC8GW8BndaybQ3Yj',
                    'title' => 'Renew Europe presents Action Plan to uphold democracy in times of COVID-19',
                    'video' => 'youtube:nxaOzonmeic',
                    'image' => null,
                    'categories' => ['data' => []],
                ],

                // Test nullable video
                [
                    'id' => '5VGqCDdNlgcLJUeNcL8hX4',
                    'title' => 'COVID19 recovery: The EU’s response to this crisis must match its magnitude',
                    'video' => null,
                ],

                // Test full list order (published at DESC)
                [
                    'id' => '4WgiNG2mipe5y51x6U3Vuf',
                    'title' => 'EU funding to Hungary must be strictly controlled by the Commission',
                ],
                [
                    'id' => '1MPQ3PlfdJtrZzMmPLhadd',
                    'title' => 'It\'s high time to improve working conditions for seasonal workers',
                ],
                [
                    'id' => '4tlOVS8GzPE0iVIKqa8bIz',
                    'title' => 'EU Recovery plan: Either we recover together or we will fail individually',
                ],
                [
                    'id' => 'ChL9MyUUHpg4GFr5lBHTo',
                    'title' => 'The EU - Western Balkans summit is a two-way commitment to European values',
                ],
                [
                    'id' => '7379hagyz9yi8PlQumwsaR',
                    'title' => 'Renew Europe welcomes the European Commission\'s firm action against Poland',
                ],
                [
                    'id' => '2xV601tkLSK9aH9sjSZQ4Y',
                    'title' => 'Policy paper on the use of contact tracing applications as part of the fight against COVID-19',
                ],
                [
                    'id' => '3c5Wi4mDtOvl2qQJDOKpjs',
                    'title' => 'COVID-19 contact tracing apps: Only a coordinated European approach can be successful',
                ],
                [
                    'id' => '1qwrYUh1XAsR9xQbvcodwI',
                    'title' => 'EU trade relations with Mexico lifted to a new level',
                ],
                [
                    'id' => '5BgsC9EaGlKllgK50WOZUE',
                    'title' => 'COVID-19: Much needed and efficient help for the EU’s fisheries sector',
                ],
            ],
        ]);
    }

    public function testListPagination()
    {
        $client = self::createClient();

        // First page
        $result = $this->apiRequest($client, 'GET', '/api/website/posts');
        $this->assertCount(12, $result['data']);

        // Mapping has already been tested in a previous test, focus on testing pagination
        $this->assertApiResponse($result, [
            'meta' => [
                'pagination' => [
                    'total' => 15,
                    'count' => 12,
                    'per_page' => 12,
                    'current_page' => 1,
                    'total_pages' => 2,
                    'links' => [
                        'next' => 'http://localhost/api/website/posts?page=2',
                    ],
                ],
            ],
            'data' => [
                ['id' => '2m4VBvTA1NbUi7acpk7JFy'],
                ['id' => '5dekUWIC8GW8BndaybQ3Yj'],
                ['id' => '5VGqCDdNlgcLJUeNcL8hX4'],
                ['id' => '4WgiNG2mipe5y51x6U3Vuf'],
                ['id' => '1MPQ3PlfdJtrZzMmPLhadd'],
                ['id' => '4tlOVS8GzPE0iVIKqa8bIz'],
                ['id' => 'ChL9MyUUHpg4GFr5lBHTo'],
                ['id' => '7379hagyz9yi8PlQumwsaR'],
                ['id' => '2xV601tkLSK9aH9sjSZQ4Y'],
                ['id' => '3c5Wi4mDtOvl2qQJDOKpjs'],
                ['id' => '1qwrYUh1XAsR9xQbvcodwI'],
                ['id' => '5BgsC9EaGlKllgK50WOZUE'],
            ],
        ]);

        // Second page
        $result = $this->apiRequest($client, 'GET', '/api/website/posts?page=2');
        $this->assertCount(3, $result['data']);

        $this->assertApiResponse($result, [
            'meta' => [
                'pagination' => [
                    'total' => 15,
                    'count' => 3,
                    'per_page' => 12,
                    'current_page' => 2,
                    'total_pages' => 2,
                    'links' => [
                        'previous' => 'http://localhost/api/website/posts?page=1',
                    ],
                ],
            ],
            'data' => [
                ['id' => '67kBeQzvCDanXfUztHtbco'],
                ['id' => '2dgQLTDw2WTPVXVEDrEzG1'],
                ['id' => '4MMFwOzfVK6zyJsVlc1wZe'],
            ],
        ]);
    }

    public function testListCategoryPosts()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/posts?category=1GmkaorS3YSezgfKGrZel1');
        $this->assertCount(2, $result['data']);

        // Mapping has already been tested in a previous test, focus on testing filtering
        $this->assertApiResponse($result, [
            'data' => [
                [
                    'id' => '2m4VBvTA1NbUi7acpk7JFy',
                    'title' => 'The EU must stand with the people of Hong Kong against China’s abuse of power',
                ],
                [
                    'id' => '1MPQ3PlfdJtrZzMmPLhadd',
                    'title' => 'It\'s high time to improve working conditions for seasonal workers',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/posts', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/posts', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/posts/2m4VBvTA1NbUi7acpk7JFy');

        // Test the payload is the one expected, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Post',
            '_links' => [
                'self' => 'http://localhost/api/website/posts/2m4VBvTA1NbUi7acpk7JFy',
            ],
            'id' => '2m4VBvTA1NbUi7acpk7JFy',
            'title' => 'The EU must stand with the people of Hong Kong against China’s abuse of power',
            'quote' => 'Quote 1',
            'slug' => 'the-eu-must-stand-with-the-people-of-hong-kong-against-china-s-abuse-of-power',
            'description' => 'Description 1',
            'video' => 'youtube:nxaOzonmeic',
            'image' => 'http://localhost/serve/post-image.png',
            'sharer' => 'http://localhost/serve/post-image.png?t=sharer',
            'content' => '<div class="row"><div class="col-md-12"><p>Content</p></div></div>',
            'categories' => [
                'data' => [
                    [
                        '_links' => [
                            'self' => 'http://localhost/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1',
                        ],
                        'id' => '1GmkaorS3YSezgfKGrZel1',
                        'name' => 'Category 2',
                        'slug' => 'category-2',
                    ],
                ],
            ],
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/posts/2m4VBvTA1NbUi7acpk7JFy', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/posts/2m4VBvTA1NbUi7acpk7JFy', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/posts/invalid', self::DEFAULT_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/posts/7rnedzqzqk0hv5ktdm3a1m', self::DEFAULT_TOKEN, 404);
    }

    public function testViewOnlyForMembers()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/posts/14nA0lolffAnALGPRxdlrN', self::DEFAULT_TOKEN, 404);
    }
}
