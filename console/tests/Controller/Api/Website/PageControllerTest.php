<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class PageControllerTest extends ApiTestCase
{
    public function testListAllPosts()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/pages');

        $this->assertCount(15, $result['data']);

        // Test content is not included in the payload
        $this->assertArrayNotHasKey('content', $result['data'][0]);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Test nullable image, empty categories and description
                [
                    'id' => '64dhFZ31PdPTzL5fS91hRj',
                    'title' => '5G : le lancement des enchères en France fixé à la fin septembre',
                    'slug' => '5g-le-lancement-des-encheres-en-france-fixe-a-la-fin-septembre',
                    'image' => null,
                    'description' => null,
                    'categories' => ['data' => []],
                    'read_time' => 0,
                ],
                [
                    'id' => '2DCuzkKxcm7Q2Ax2yyRq53',
                    'title' => 'Alcohol',
                ],
                // Test full mapping
                [
                    '_resource' => 'Page',
                    '_links' => [
                        'self' => 'http://localhost/api/website/pages/dVZJqhjijMngI6ZIzUV88',
                    ],
                    'id' => 'dVZJqhjijMngI6ZIzUV88',
                    'title' => 'Coronavirus',
                    'slug' => 'coronavirus',
                    'description' => 'Coronavirus disease (COVID-19) is an infectious disease caused by a newly discovered coronavirus.',
                    'image' => 'https://localhost:8000/serve/page-image.png',
                    'sharer' => 'https://localhost:8000/serve/page-image.png?t=sharer',
                    'categories' => [
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
                        ],
                    ],
                ],
                [
                    'id' => '7RUxJUKW754jgw471OtmA7',
                    'title' => 'Crash économique sans précédent au Brésil, en pleine crise sanitaire et politique',
                ],
                [
                    'id' => '1Fi5auMlM7PqNfqzCJ8lTZ',
                    'title' => 'Dengue and severe dengue',
                ],
                [
                    'id' => '1s0fz9qYei4FAT2flX2WTX',
                    'title' => 'Diabetes',
                ],
                [
                    'id' => '7lTNUowFFvCQcuR8Nijx0p',
                    'title' => 'Ebola virus disease',
                ],
                [
                    'id' => 'aOrsyO60E7tHROHKPPnRg',
                    'title' => 'Emmanuel Macron, la tentation d\'une démission-réélection',
                    'slug' => 'emmanuel-macron-la-tentation-d-une-demission-reelection',
                ],
                [
                    'id' => 'qwKX3iOaQJWNzMAczfGcs',
                    'title' => 'How the Economy Will Look After the Coronavirus Pandemic',
                ],
                [
                    'id' => '1y87ULopiMhHdlZDMKL3sS',
                    'title' => 'La dette, solution et problème de la crise économique',
                ],
                [
                    'id' => '36QlAr3vWTE7fezlKvILNg',
                    'title' => 'L\'économie française a détruit un demi-million d’emplois au premier trimestre 2020',
                ],
                [
                    'id' => 'C55KcZa6k4y3iEB623caG',
                    'title' => 'Les chauffeurs d\'Uber et de Lyft sont des salariés, répète le régulateur californien',
                ],
                [
                    'id' => '25n1dpQwuh1r2JLDdLDn9V',
                    'title' => 'Les élus locaux plaident pour une relance',
                ],
                [
                    'id' => '6HmZ07gjhFv0PKBqvutgBu',
                    'title' => 'Stéphane Lissner: \'L\'Opéra de Paris est à genoux\'',
                ],
                [
                    'id' => 'ZcmpSIx06HwFmfWjiuMPu',
                    'title' => 'Theory of relativity',
                ],
            ],
        ]);
    }

    public function testListCategoryPages()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/pages?category=7hIQY74GJcZWKsJxafwbHZ');

        $this->assertCount(2, $result['data']);

        // Mapping has already been tested in a previous test, focus on testing filtering
        $this->assertApiResponse($result, [
            'data' => [
                [
                    'id' => 'dVZJqhjijMngI6ZIzUV88',
                    'title' => 'Coronavirus',
                ],
                [
                    'id' => 'qwKX3iOaQJWNzMAczfGcs',
                    'title' => 'How the Economy Will Look After the Coronavirus Pandemic',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/pages', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/pages', 'invalid', 401);
    }

    public function testViewNoChildren()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/pages/dVZJqhjijMngI6ZIzUV88');

        // Test the payload, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Page',
            '_links' => [
                'self' => 'http://localhost/api/website/pages/dVZJqhjijMngI6ZIzUV88',
            ],
            'id' => 'dVZJqhjijMngI6ZIzUV88',
            'title' => 'Coronavirus',
            'slug' => 'coronavirus',
            'description' => 'Coronavirus disease (COVID-19) is an infectious disease caused by a newly discovered coronavirus.',
            'content' => '<div class="row"><div class="col-md-12"><p>Content</p></div></div>',
            'image' => 'https://localhost:8000/serve/page-image.png',
            'sharer' => 'https://localhost:8000/serve/page-image.png?t=sharer',
            'categories' => [
                'data' => [
                    [
                        'id' => '7hIQY74GJcZWKsJxafwbHZ',
                        'name' => 'Category 2',
                        'slug' => 'category-2',
                        '_links' => [
                            'self' => 'http://localhost/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ',
                        ],
                    ],
                ],
            ],
            'children' => ['data' => []],
        ]);
    }

    public function testViewSlug()
    {
        $client = self::createClient();
        $result = $this->apiRequest($client, 'GET', '/api/website/pages/coronavirus');
        $this->assertApiResponse($result, ['id' => 'dVZJqhjijMngI6ZIzUV88']);
    }

    public function testViewWithChildren()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/pages/1k8qbksfGCGNJTlcecs8nd', self::ACME_TOKEN);

        // Test the payload, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Page',
            '_links' => [
                'self' => 'http://localhost/api/website/pages/1k8qbksfGCGNJTlcecs8nd',
            ],
            'id' => '1k8qbksfGCGNJTlcecs8nd',
            'title' => 'Emmanuel Macron, la tentation d\'une démission réélection',
            'slug' => 'emmanuel-macron-la-tentation-d-une-demission-reelection',
            'description' => null,
            'content' => '',
            'image' => null,
            'sharer' => null,
            'read_time' => 0,
            'categories' => ['data' => []],
            'children' => [
                'data' => [
                    [
                        '_resource' => 'Page',
                        '_links' => [
                            'self' => 'http://localhost/api/website/pages/1PHbHzada4anX9PeRWyY5p',
                        ],
                        'id' => '1PHbHzada4anX9PeRWyY5p',
                        'title' => 'Subpage example',
                        'slug' => 'subpage-example',
                        'description' => null,
                        'image' => null,
                        'sharer' => null,
                    ],
                ],
            ],
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/pages/dVZJqhjijMngI6ZIzUV88', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/pages/dVZJqhjijMngI6ZIzUV88', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/pages/invalid', self::EXAMPLECO_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/pages/7rnedzqzqk0hv5ktdm3a1m', self::EXAMPLECO_TOKEN, 404);
    }

    public function testViewOnlyForMembers()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/pages/14nA0lolffAnALGPRxdlrN', self::EXAMPLECO_TOKEN, 404);
    }
}
