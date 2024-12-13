<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class EventControllerTest extends ApiTestCase
{
    public function testListAllEvents()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/events');
        $this->assertCount(12, $result['data']);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Test full mapping
                [
                    '_resource' => 'Event',
                    '_links' => [
                        'self' => 'http://localhost/api/website/events/5BehrTYzzuXwHL9Hc3cZHf',
                    ],
                    'id' => '5BehrTYzzuXwHL9Hc3cZHf',
                    'title' => 'Event 1',
                    'slug' => 'event-1',
                    'content' => 'Event content',
                    'externalUrl' => 'https://openaction.eu',
                    'url' => 'https://citipo.com',
                    'buttonText' => 'Click here',
                    'latitude' => '1.2345000',
                    'longitude' => '6.7890000',
                    'address' => 'Event address',
                    'image' => 'http://localhost/serve/event-image.png',
                    'sharer' => 'http://localhost/serve/event-image.png?t=sharer',
                    'form' => 'https://exampleco.com/_redirect/form/4wxrTbH3IvFqnMdO3L789k',
                    'categories' => [
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
                        ],
                    ],
                    'participants' => [
                        'data' => [
                            [
                                '_resource' => 'TrombinoscopePersonLight',
                                '_links' => ['self' => 'http://localhost/api/website/trombinoscope/29w2ahAQA0Rbaa0FVTBHay'],
                                'id' => '29w2ahAQA0Rbaa0FVTBHay',
                                'slug' => 'nathalie-loiseau',
                                'fullName' => 'Nathalie Loiseau',
                                'position' => 1,
                                'image' => null,
                            ],
                        ],
                    ],
                ],

                // Test nullable image, empty categories
                [
                    'id' => 'W3oQ8Zg3HN6L6kiQY7RPn',
                    'title' => 'Event 11',
                    'image' => null,
                    'categories' => ['data' => []],
                ],

                // Test full list order (published at DESC)
                [
                    'id' => '3Dj8jnJI7Bj4qyRX7qIuj9',
                    'title' => 'Event 10',
                ],
                [
                    'id' => '6ORlxW0JxBBZyTkMIIjQ5q',
                    'title' => 'Event 9',
                ],
                [
                    'id' => '2ZOl7G6BAbdD6IcPrYkXtJ',
                    'title' => 'Event 8',
                ],
                [
                    'id' => '7X1Lu1OZZHCAOzgpK6Ghlt',
                    'title' => 'Event 7',
                ],
                [
                    'id' => 'mdMnyPJ9FMjqlbb20GYBL',
                    'title' => 'Event 6',
                ],
                [
                    'id' => '4trTSQf0LYafcliLQSMQpb',
                    'title' => 'Event 5',
                ],
                [
                    'id' => '1BpPBRZMqsS1r2TwCisPCM',
                    'title' => 'Event 4',
                ],
                [
                    'id' => 'M5LPWMJ25EYJ8uhVfVc6e',
                    'title' => 'Event 3',
                    'categories' => [
                        'data' => [
                            [
                                'name' => 'Category 1',
                                'slug' => 'category-1',
                            ],
                            [
                                'name' => 'Category 2',
                                'slug' => 'category-2',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '3UBWxmHiKHdwROPoANfam7',
                    'title' => 'Event 2',
                ],
            ],
        ]);
    }

    public function testListPagination()
    {
        $client = self::createClient();

        // First page
        $result = $this->apiRequest($client, 'GET', '/api/website/events');
        $this->assertCount(12, $result['data']);

        // Mapping has already been tested in a previous test, focus on testing pagination
        $this->assertApiResponse($result, [
            'meta' => [
                'pagination' => [
                    'total' => 13,
                    'count' => 12,
                    'per_page' => 12,
                    'current_page' => 1,
                    'total_pages' => 2,
                    'links' => [
                        'next' => 'http://localhost/api/website/events?page=2',
                    ],
                ],
            ],
            'data' => [
                ['id' => '5BehrTYzzuXwHL9Hc3cZHf'],
                ['id' => 'W3oQ8Zg3HN6L6kiQY7RPn'],
                ['id' => '3Dj8jnJI7Bj4qyRX7qIuj9'],
                ['id' => '6ORlxW0JxBBZyTkMIIjQ5q'],
                ['id' => '2ZOl7G6BAbdD6IcPrYkXtJ'],
                ['id' => '7X1Lu1OZZHCAOzgpK6Ghlt'],
                ['id' => 'mdMnyPJ9FMjqlbb20GYBL'],
                ['id' => '4trTSQf0LYafcliLQSMQpb'],
                ['id' => '1BpPBRZMqsS1r2TwCisPCM'],
                ['id' => 'M5LPWMJ25EYJ8uhVfVc6e'],
                ['id' => '3UBWxmHiKHdwROPoANfam7'],
                ['id' => '5LMquMd95n11iBKN0Ywcsg'],
            ],
        ]);

        // Second page
        $result = $this->apiRequest($client, 'GET', '/api/website/events?page=2');
        $this->assertCount(1, $result['data']);

        $this->assertApiResponse($result, [
            'meta' => [
                'pagination' => [
                    'total' => 13,
                    'count' => 1,
                    'per_page' => 12,
                    'current_page' => 2,
                    'total_pages' => 2,
                    'links' => [
                        'previous' => 'http://localhost/api/website/events?page=1',
                    ],
                ],
            ],
            'data' => [
                ['id' => 'B9sNSxNde0NfdSZK5tm81'],
            ],
        ]);
    }

    public function testListCategoryEvents()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/events?category=1r1BjY3gmpXo6e4KH7kMNU');
        $this->assertCount(2, $result['data']);

        // Mapping has already been tested in a previous test, focus on testing filtering
        $this->assertApiResponse($result, [
            'data' => [
                [
                    'id' => '5BehrTYzzuXwHL9Hc3cZHf',
                    'title' => 'Event 1',
                ],
                [
                    'id' => 'M5LPWMJ25EYJ8uhVfVc6e',
                    'title' => 'Event 3',
                ],
            ],
        ]);
    }

    public function testListParticipantEvents()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/events?participant=29w2ahAQA0Rbaa0FVTBHay');
        $this->assertCount(1, $result['data']);

        // Mapping has already been tested in a previous test, focus on testing filtering
        $this->assertApiResponse($result, [
            'data' => [
                [
                    'id' => '5BehrTYzzuXwHL9Hc3cZHf',
                    'title' => 'Event 1',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/events', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/events', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/events/5BehrTYzzuXwHL9Hc3cZHf');

        $this->assertApiResponse($result, [
            '_resource' => 'Event',
            '_links' => [
                'self' => 'http://localhost/api/website/events/5BehrTYzzuXwHL9Hc3cZHf',
            ],
            'id' => '5BehrTYzzuXwHL9Hc3cZHf',
            'title' => 'Event 1',
            'slug' => 'event-1',
            'content' => 'Event content',
            'url' => 'https://citipo.com',
            'buttonText' => 'Click here',
            'latitude' => '1.2345000',
            'longitude' => '6.7890000',
            'address' => 'Event address',
            'image' => 'http://localhost/serve/event-image.png',
            'sharer' => 'http://localhost/serve/event-image.png?t=sharer',
            'categories' => [
                'data' => [
                    [
                        '_links' => [
                            'self' => 'http://localhost/api/website/events-categories/1r1BjY3gmpXo6e4KH7kMNU',
                        ],
                        'id' => '1r1BjY3gmpXo6e4KH7kMNU',
                        'name' => 'Category 1',
                        'slug' => 'category-1',
                    ],
                ],
            ],
        ]);
    }

    public function testViewSlug()
    {
        $client = self::createClient();
        $result = $this->apiRequest($client, 'GET', '/api/website/events/event-1');
        $this->assertApiResponse($result, ['id' => '5BehrTYzzuXwHL9Hc3cZHf']);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/events/5BehrTYzzuXwHL9Hc3cZHf', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/events/5BehrTYzzuXwHL9Hc3cZHf', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/events/invalid', self::EXAMPLECO_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/events/7rnedzqzqk0hv5ktdm3a1m', self::EXAMPLECO_TOKEN, 404);
    }

    public function testViewOnlyForMembers()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/events/14nA0lolffAnALGPRxdlrN', self::EXAMPLECO_TOKEN, 404);
    }
}
