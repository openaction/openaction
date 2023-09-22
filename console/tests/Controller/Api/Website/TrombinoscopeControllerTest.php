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

        // Test content is not included in the payload
        $this->assertArrayNotHasKey('content', $result['data'][0]);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Test full mapping
                [
                    '_resource' => 'TrombinoscopePerson',
                    '_links' => [
                        'self' => 'http://localhost/api/website/trombinoscope/3gQjYZ1UYDaaBmOcian0vT',
                    ],
                    'id' => '3gQjYZ1UYDaaBmOcian0vT',
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
                    'id' => '7cKpEjgYnJ8hf1DbJLGTLe',
                    'fullName' => 'Marie-Pierre Vedrenne',
                    'categories' => [
                        'data' => [
                            ['name' => 'Loire-Atlantique'],
                        ],
                    ],
                ],
                [
                    'id' => '5VFDQAl2AW1HwhL40dSzKN',
                    'fullName' => 'Jérémy Decerle',
                    'categories' => [
                        'data' => [
                            ['name' => 'Eure-et-Loir'],
                        ],
                    ],
                ],
                [
                    'id' => '2pV9apAl7qsVofSamMPJ9J',
                    'fullName' => 'Catherine Chabaud',
                    'categories' => [
                        'data' => [],
                    ],
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

        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/3gQjYZ1UYDaaBmOcian0vT', self::ACME_TOKEN);

        // Test the payload is the one expected, including content
        $this->assertApiResponse($result, [
            '_resource' => 'TrombinoscopePerson',
            '_links' => [
                'self' => 'http://localhost/api/website/trombinoscope/3gQjYZ1UYDaaBmOcian0vT',
            ],
            'id' => '3gQjYZ1UYDaaBmOcian0vT',
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
        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/3gQjYZ1UYDaaBmOcian0vT?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            'id' => '3gQjYZ1UYDaaBmOcian0vT',
            'fullName' => 'Nathalie Loiseau',
            'next' => [
                'id' => '7cKpEjgYnJ8hf1DbJLGTLe',
                'fullName' => 'Marie-Pierre Vedrenne',
            ],
        ]);

        // Center person: previous and next
        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/7cKpEjgYnJ8hf1DbJLGTLe?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            '_resource' => 'TrombinoscopePerson',
            'id' => '7cKpEjgYnJ8hf1DbJLGTLe',
            'fullName' => 'Marie-Pierre Vedrenne',
            'previous' => [
                'id' => '3gQjYZ1UYDaaBmOcian0vT',
                'fullName' => 'Nathalie Loiseau',
            ],
            'next' => [
                'id' => '5VFDQAl2AW1HwhL40dSzKN',
                'fullName' => 'Jérémy Decerle',
            ],
        ]);

        // Last person: only previous
        $result = $this->apiRequest($client, 'GET', '/api/website/trombinoscope/2pV9apAl7qsVofSamMPJ9J?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            '_resource' => 'TrombinoscopePerson',
            'id' => '2pV9apAl7qsVofSamMPJ9J',
            'fullName' => 'Catherine Chabaud',
            'previous' => [
                'id' => '5VFDQAl2AW1HwhL40dSzKN',
                'fullName' => 'Jérémy Decerle',
            ],
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope/3gQjYZ1UYDaaBmOcian0vT', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/trombinoscope/3gQjYZ1UYDaaBmOcian0vT', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/trombinoscope/invalid', self::ACME_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/trombinoscope/7rnedzqzqk0hv5ktdm3a1m', self::EXAMPLECO_TOKEN, 404);
    }
}
