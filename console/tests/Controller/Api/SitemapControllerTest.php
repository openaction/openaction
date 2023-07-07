<?php

namespace App\Tests\Controller\Api;

use App\Tests\ApiTestCase;

class SitemapControllerTest extends ApiTestCase
{
    public function provideSitemap(): iterable
    {
        yield [ApiTestCase::DEFAULT_TOKEN];
        yield [ApiTestCase::CITIPO_TOKEN];
        yield [ApiTestCase::ACME_TOKEN];
    }

    /**
     * @dataProvider provideSitemap
     */
    public function testSitemap(string $token): void
    {
        $client = self::createClient();
        $sections = [
            'pages',
            'posts',
            'postCategories',
            'events',
            'eventCategories',
            'forms',
            'documents',
            'trombinoscope',
            'manifesto',
        ];

        $result = $this->apiRequest($client, 'GET', '/api/project/sitemap', $token);

        foreach ($result as $label => $entities) {
            $this->assertContains($label, $sections);

            foreach ($entities as $entity) {
                $this->assertNotEmpty($entity['id']);
                $this->assertIsString($entity['slug']);

                if (isset($entity['updatedAt'])) {
                    new \DateTime($entity['updatedAt']);
                    $this->addToAssertionCount(1);
                }
            }
        }
    }

    public function testSitemapNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/project/sitemap', null, 401);
    }

    public function testSitemapInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/project/sitemap', 'invalid', 401);
    }
}
