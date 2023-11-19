<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class TrombinoscopeControllerTest extends ApiTestCase
{
    public function testListAll()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope', self::ACME_TOKEN);
        $this->assertCount(4, $result['data']);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Test full mapping
                [
                    '_resource' => 'TrombinoscopePerson',
                    '_links' => [
                        'self' => 'http://localhost/api/website/trombinoscope/29w2ahAQA0Rbaa0FVTBHay',
                    ],
                    'id' => '29w2ahAQA0Rbaa0FVTBHay',
                    'fullName' => 'Nathalie Loiseau',
                    'role' => 'Tête de liste Renaissance pour les élections européennes. (Île-de-France).',
                    'content' => '<div class="row"><div class="col-md-12"><p>Content</p></div></div>',
                    'socialEmail' => 'nathalie.loiseau@example.org',
                    'socialFacebook' => 'https://facebook.com',
                    'socialTwitter' => 'https://twitter.com',
                    'socialInstagram' => 'https://instagram.com',
                    'socialLinkedIn' => 'https://linkedin.com',
                    'socialYoutube' => 'https:/youtube.com',
                    'socialMedium' => 'https://medium.com',
                    'socialTelegram' => 'nathalie.loiseau',
                    'image' => null,
                    'categories' => [
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
                    ],
                ],

                // Test full list order (weight ASC)
                [
                    'id' => '6Hm17nqmgYzMbeUmgY2wPn',
                    'fullName' => 'Marie-Pierre Vedrenne',
                    'categories' => [
                        'data' => [
                            ['name' => 'Loire-Atlantique'],
                        ],
                    ],
                ],
                [
                    'id' => '5AtJEWju4gaSO7Z0xKBJtw',
                    'fullName' => 'Jérémy Decerle',
                    'categories' => [
                        'data' => [
                            ['name' => 'Eure-et-Loir'],
                        ],
                    ],
                ],
                [
                    'id' => '4btW2uTPrB9dYfJgl8QPwq',
                    'fullName' => 'Catherine Chabaud',
                    'categories' => [
                        'data' => [],
                    ],
                ],
            ],
        ]);
    }

    public function testListCategory()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope?category=7Fy7Er4zVNHhotuL9q2JfQ', self::ACME_TOKEN);
        $this->assertCount(2, $result['data']);

        $this->assertApiResponse($result, [
            'data' => [
                [
                    'id' => '29w2ahAQA0Rbaa0FVTBHay',
                    'fullName' => 'Nathalie Loiseau',
                ],
                [
                    'id' => '6Hm17nqmgYzMbeUmgY2wPn',
                    'fullName' => 'Marie-Pierre Vedrenne',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/29w2ahAQA0Rbaa0FVTBHay', self::ACME_TOKEN);

        // Test the payload is the one expected, including content
        $this->assertApiResponse($result, [
            '_resource' => 'TrombinoscopePerson',
            '_links' => [
                'self' => 'http://localhost/api/website/trombinoscope/29w2ahAQA0Rbaa0FVTBHay',
            ],
            'id' => '29w2ahAQA0Rbaa0FVTBHay',
            'fullName' => 'Nathalie Loiseau',
            'role' => 'Tête de liste Renaissance pour les élections européennes. (Île-de-France).',
            'socialEmail' => 'nathalie.loiseau@example.org',
            'socialFacebook' => 'https://facebook.com',
            'socialTwitter' => 'https://twitter.com',
            'socialInstagram' => 'https://instagram.com',
            'socialLinkedIn' => 'https://linkedin.com',
            'socialYoutube' => 'https:/youtube.com',
            'socialMedium' => 'https://medium.com',
            'socialTelegram' => 'nathalie.loiseau',
            'image' => null,
            'content' => '<div class="row"><div class="col-md-12"><p>Content</p></div></div>',
        ]);
    }

    public function testViewPreviousNext()
    {
        $client = self::createClient();

        // First person: only next
        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/29w2ahAQA0Rbaa0FVTBHay?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            'id' => '29w2ahAQA0Rbaa0FVTBHay',
            'fullName' => 'Nathalie Loiseau',
            'next' => [
                'id' => '6Hm17nqmgYzMbeUmgY2wPn',
                'fullName' => 'Marie-Pierre Vedrenne',
            ],
        ]);

        // Center person: previous and next
        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/6Hm17nqmgYzMbeUmgY2wPn?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            '_resource' => 'TrombinoscopePerson',
            'id' => '6Hm17nqmgYzMbeUmgY2wPn',
            'fullName' => 'Marie-Pierre Vedrenne',
            'previous' => [
                'id' => '29w2ahAQA0Rbaa0FVTBHay',
                'fullName' => 'Nathalie Loiseau',
            ],
            'next' => [
                'id' => '5AtJEWju4gaSO7Z0xKBJtw',
                'fullName' => 'Jérémy Decerle',
            ],
        ]);

        // Last person: only previous
        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/4btW2uTPrB9dYfJgl8QPwq?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            '_resource' => 'TrombinoscopePerson',
            'id' => '4btW2uTPrB9dYfJgl8QPwq',
            'fullName' => 'Catherine Chabaud',
            'previous' => [
                'id' => '5AtJEWju4gaSO7Z0xKBJtw',
                'fullName' => 'Jérémy Decerle',
            ],
        ]);
    }

    public function testViewPosts()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/29w2ahAQA0Rbaa0FVTBHay?includes=previous,next,posts', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            'id' => '29w2ahAQA0Rbaa0FVTBHay',
            'fullName' => 'Nathalie Loiseau',
            'posts' => [
                'data' => [
                    [
                        '_resource' => 'Post',
                        '_links' => ['self' => 'http://localhost/api/website/posts/2m4VBvTA1NbUi7acpk7JFy'],
                        'id' => '2m4VBvTA1NbUi7acpk7JFy',
                        'title' => 'The EU must stand with the people of Hong Kong against China’s abuse of power',
                        'quote' => 'Quote 1',
                        'slug' => 'the-eu-must-stand-with-the-people-of-hong-kong-against-china-s-abuse-of-power',
                        'description' => 'Description 1',
                        'externalUrl' => 'https://openaction.eu',
                        'video' => 'youtube:nxaOzonmeic',
                        'image' => 'http://localhost/serve/post-image.png',
                        'sharer' => 'http://localhost/serve/post-image.png?t=sharer',
                    ],
                ],
            ],
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope/29w2ahAQA0Rbaa0FVTBHay', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope/29w2ahAQA0Rbaa0FVTBHay', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/trombinoscope/invalid', self::ACME_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/trombinoscope/7rnedzqzqk0hv5ktdm3a1m', self::EXAMPLECO_TOKEN, 404);
    }
}
