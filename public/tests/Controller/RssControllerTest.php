<?php

namespace App\Tests\Controller;

use App\Client\CitipoInterface;
use App\Client\Model\ApiResource;
use App\Client\PassKey\TokenResolver;
use App\Feed\EnclosureMetadataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Cache\CacheInterface;

class RssControllerTest extends WebTestCase
{
    public function testRssFeedIncludesImageEnclosureWhenMetadataAvailable(): void
    {
        $client = self::createClient();

        $this->stubDependencies(
            $this->createProject(),
            $this->createSitemap([
                [
                    'id' => 'post-1',
                    'slug' => 'post',
                    'title' => 'My post',
                    'description' => 'Post description',
                    'createdAt' => '2024-01-02T10:00:00+00:00',
                    'updatedAt' => '2024-01-03T10:00:00+00:00',
                    'image' => 'https://cdn.example.com/post.jpg',
                ],
            ]),
            new MockHttpClient([
                new MockResponse('', [
                    'response_headers' => [
                        'Content-Length: 2048',
                        'Content-Type: image/png',
                    ],
                ]),
            ])
        );

        $client->request('GET', '/rss.xml');
        $this->assertResponseIsSuccessful();

        $feed = simplexml_load_string($client->getResponse()->getContent());
        $this->assertInstanceOf(\SimpleXMLElement::class, $feed);

        $item = $feed->channel->item;
        $this->assertNotEmpty($item);
        $this->assertSame('https://cdn.example.com/post.jpg', (string) $item[0]->enclosure['url']);
        $this->assertSame('2048', (string) $item[0]->enclosure['length']);
        $this->assertSame('image/png', (string) $item[0]->enclosure['type']);
    }

    public function testRssFeedSkipsImageEnclosureWhenMetadataLengthMissing(): void
    {
        $client = self::createClient();

        $this->stubDependencies(
            $this->createProject(),
            $this->createSitemap([
                [
                    'id' => 'post-2',
                    'slug' => 'post-without-length',
                    'title' => 'Missing length',
                    'description' => 'Post description',
                    'createdAt' => '2024-01-04T10:00:00+00:00',
                    'updatedAt' => '2024-01-05T10:00:00+00:00',
                    'image' => 'https://cdn.example.com/without-length.jpg',
                ],
            ]),
            new MockHttpClient([
                new MockResponse('', [
                    'response_headers' => [
                        'Content-Type: image/jpeg',
                    ],
                ]),
            ])
        );

        $client->request('GET', '/rss.xml');
        $this->assertResponseIsSuccessful();

        $feed = simplexml_load_string($client->getResponse()->getContent());
        $this->assertInstanceOf(\SimpleXMLElement::class, $feed);

        $item = $feed->channel->item;
        $this->assertNotEmpty($item);
        $this->assertEmpty($item[0]->xpath('enclosure'));
    }

    private function stubDependencies(ApiResource $project, ApiResource $sitemap, MockHttpClient $httpClient): void
    {
        $container = self::getContainer();

        $tokenResolver = $this->createMock(TokenResolver::class);
        $tokenResolver->method('resolveProjectToken')->willReturn('token');
        $container->set(TokenResolver::class, $tokenResolver);

        $citipo = $this->createMock(CitipoInterface::class);
        $citipo->method('getProject')->willReturn($project);
        $citipo->method('getProjectSitemap')->willReturn($sitemap);
        $container->set(CitipoInterface::class, $citipo);

        /** @var CacheInterface $cache */
        $cache = $container->get(CacheInterface::class);
        $container->get('cache.app')->clear();

        $container->set(EnclosureMetadataProvider::class, new EnclosureMetadataProvider($httpClient, $cache));
    }

    private function createProject(): ApiResource
    {
        $project = new ApiResource();
        $project->name = 'Project';
        $project->metaTitle = 'Project title';
        $project->metaDescription = 'Project description';
        $project->locale = 'en';
        $project->access = ['username' => null, 'password' => null];
        $project->tools = [];
        $project->redirections = [];
        $project->importedRedirections = [];

        return $project;
    }

    private function createSitemap(array $posts): ApiResource
    {
        $sitemap = new ApiResource();
        $sitemap->posts = $posts;
        $sitemap->pages = [];
        $sitemap->postCategories = [];
        $sitemap->events = [];
        $sitemap->eventCategories = [];
        $sitemap->forms = [];
        $sitemap->documents = [];
        $sitemap->trombinoscope = [];
        $sitemap->manifesto = [];

        return $sitemap;
    }
}
