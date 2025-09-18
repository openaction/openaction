<?php

namespace App\Tests\Controller;

use App\Client\CitipoInterface;
use App\Client\Model\ApiResource;
use App\Client\PassKey\TokenResolver;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapPetitionsTest extends WebTestCase
{
    public function testSitemapIncludesPetitions(): void
    {
        $client = self::createClient();

        // Stub token resolver
        $tokenResolver = new class extends TokenResolver {
            public function __construct()
            {
            }

            public function resolveProjectToken(string $domain): ?string
            {
                return 'TEST_TOKEN';
            }
        };
        static::getContainer()->set(TokenResolver::class, $tokenResolver);

        // Mock Citipo client
        $citipo = $this->createMock(CitipoInterface::class);

        $project = new ApiResource();
        $project->name = 'Test Project';
        $project->locale = 'fr';
        $project->tools = ['website_petitions'];
        $project->access = ['username' => null, 'password' => null];
        $project->redirections = [];
        $project->importedRedirections = [];
        $citipo->method('getProject')->willReturn($project);

        $sitemapData = new ApiResource();
        // Required keys by SitemapController's ROUTES_BINDINGS
        $sitemapData->pages = [];
        $sitemapData->posts = [];
        $sitemapData->postCategories = [];
        $sitemapData->events = [];
        $sitemapData->eventCategories = [];
        $sitemapData->forms = [];
        $sitemapData->documents = [];
        // Our petitions entry
        $sitemapData->petitions = [
            [
                'slug' => 'stop-foo',
                'updatedAt' => '2025-01-01T00:00:00+00:00',
            ],
        ];
        $citipo->method('getProjectSitemap')->willReturn($sitemapData);

        static::getContainer()->set(CitipoInterface::class, $citipo);

        $client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertStringContainsString('<loc>http://localhost/pe/stop-foo/fr</loc>', $content);
    }
}
