<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class ManifestoControllerTest extends ApiTestCase
{
    public function testListAll()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/manifesto', self::ACME_TOKEN);
        $this->assertCount(3, $result['data']);

        // Test content is not included in the payload
        $this->assertArrayNotHasKey('content', $result['data'][0]);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Test full mapping
                [
                    '_resource' => 'ManifestoTopic',
                    '_links' => [
                        'self' => 'http://localhost/api/website/manifesto/2ybrHJLAHyD4O6r9WFhe1r',
                    ],
                    'id' => '2ybrHJLAHyD4O6r9WFhe1r',
                    'title' => 'Pour une ville plus durable',
                    'slug' => 'pour-une-ville-plus-durable',
                    'description' => null,
                    'image' => null,
                    'sharer' => null,
                ],

                // Test full list order (weight ASC)
                [
                    'id' => '64eaD49uHBTEiq1v5PDgDl',
                    'title' => 'Pour une ville plus sûre',
                ],
                [
                    'id' => '16QHUpHBYittMEcLsvu74y',
                    'title' => 'Pour une ville plus tranquille',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/manifesto', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/manifesto', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/manifesto/2ybrHJLAHyD4O6r9WFhe1r', self::ACME_TOKEN);

        // Test the payload is the one expected, including content
        $this->assertApiResponse($result, [
            '_resource' => 'ManifestoTopic',
            '_links' => [
                'self' => 'http://localhost/api/website/manifesto/2ybrHJLAHyD4O6r9WFhe1r',
            ],
            'id' => '2ybrHJLAHyD4O6r9WFhe1r',
            'title' => 'Pour une ville plus durable',
            'slug' => 'pour-une-ville-plus-durable',
            'description' => null,
            'image' => null,
            'sharer' => null,
            'proposals' => [
                [
                    'title' => 'Donnons la priorité à vos trajets quotidiens',
                    'content' => '<p>Exiger la réciprocité en matière de marchés publics</p>',
                    'status' => 'in_progress',
                    'statusDescription' => 'Cette proposition est en cours de discution au Parlement.',
                    'statusCtaText' => 'Voir les débats parlementaires',
                    'statusCtaUrl' => 'https://www.youtube.com/watch?v=fQqWza-encA',
                ],
                [
                    'title' => 'Agissons pour une ville plus propre, durablement',
                    'content' => '<p>Étendre les AOP aux produits issus de l’artisanat de nos régions</p>',
                    'status' => null,
                    'statusDescription' => null,
                    'statusCtaText' => null,
                    'statusCtaUrl' => null,
                ],
            ],
        ]);
    }

    public function testViewPreviousNext()
    {
        $client = self::createClient();

        // First topic: only next
        $result = $this->apiRequest($client, 'GET', '/api/website/manifesto/2ybrHJLAHyD4O6r9WFhe1r?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            'id' => '2ybrHJLAHyD4O6r9WFhe1r',
            'title' => 'Pour une ville plus durable',
            'next' => [
                'id' => '64eaD49uHBTEiq1v5PDgDl',
                'title' => 'Pour une ville plus sûre',
            ],
        ]);

        // Center topic: previous and next
        $result = $this->apiRequest($client, 'GET', '/api/website/manifesto/64eaD49uHBTEiq1v5PDgDl?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            'id' => '64eaD49uHBTEiq1v5PDgDl',
            'title' => 'Pour une ville plus sûre',
            'previous' => [
                'id' => '2ybrHJLAHyD4O6r9WFhe1r',
                'title' => 'Pour une ville plus durable',
            ],
            'next' => [
                'id' => '16QHUpHBYittMEcLsvu74y',
                'title' => 'Pour une ville plus tranquille',
            ],
        ]);

        // Last topic: only previous
        $result = $this->apiRequest($client, 'GET', '/api/website/manifesto/16QHUpHBYittMEcLsvu74y?includes=previous,next', self::ACME_TOKEN);

        $this->assertApiResponse($result, [
            'id' => '16QHUpHBYittMEcLsvu74y',
            'title' => 'Pour une ville plus tranquille',
            'previous' => [
                'id' => '64eaD49uHBTEiq1v5PDgDl',
                'title' => 'Pour une ville plus sûre',
            ],
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/manifesto/2ybrHJLAHyD4O6r9WFhe1r', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/manifesto/2ybrHJLAHyD4O6r9WFhe1r', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/manifesto/invalid', self::ACME_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/manifesto/7rnedzqzqk0hv5ktdm3a1m', self::ACME_TOKEN, 404);
    }
}
