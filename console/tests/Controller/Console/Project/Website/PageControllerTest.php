<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\Page;
use App\Repository\ProjectRepository;
use App\Repository\Website\PageCategoryRepository;
use App\Repository\Website\PageRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Util\Uid;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class PageControllerTest extends WebTestCase
{
    private const PAGE_THEORY_UUID = '30f26de9-fe21-4d24-9b17-217d02156ac9';

    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages');
        $this->assertResponseIsSuccessful();

        $this->assertCount(2, $crawler->filter('.world-list-row'));
        $this->assertStringEndsWith('/res/images/default.jpg', $crawler->selectImage('Theory of Everything')->image()->getUri());
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#page-editor');
    }

    public function provideUpdateContent(): iterable
    {
        yield ['Title', 'my html'];
    }

    /**
     * @dataProvider provideUpdateContent
     */
    public function testUpdateContent(string $title, string $content)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/update/content',
            ['page' => ['title' => $title, 'content' => $content]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        $page = static::getContainer()->get(PageRepository::class)->findOneBy(['uuid' => self::PAGE_THEORY_UUID]);
        $this->assertEquals($title, $page->getTitle());
        $this->assertEquals($content, $page->getContent());
    }

    public function providUpdateContentInvalid()
    {
        yield ['', ''];
        yield [str_repeat('x', 300), ''];
    }

    /**
     * @dataProvider providUpdateContentInvalid
     */
    public function testUpdateContentInvalidData(string $title, string $content)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/update/content',
            ['page' => ['title' => $title, 'content' => $content]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateContentInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/update/content');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function provideUpdateMetadata(): iterable
    {
        yield ['description', '1'];
        yield ['description', '0'];
    }

    /**
     * @dataProvider provideUpdateMetadata
     */
    public function testUpdateMetadata(string $description, string $onlyForMembers)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/update/metadata',
            [
                'page' => [
                    'description' => $description,
                    'categories' => '',
                    'onlyForMembers' => $onlyForMembers,
                ],
            ],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var Page $page */
        $page = static::getContainer()->get(PageRepository::class)->findOneBy(['uuid' => self::PAGE_THEORY_UUID]);
        $this->assertEquals($description, $page->getDescription());
        $this->assertSame((bool) $onlyForMembers, $page->isOnlyForMembers());
    }

    public function provideUploadImage(): iterable
    {
        yield 'pdf' => [
            'count' => 1,
            'filename' => 'document.pdf',
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedAdded' => false,
        ];

        yield 'png' => [
            'count' => 2,
            'filename' => 'mario.png',
            'expectedStatus' => Response::HTTP_OK,
            'expectedAdded' => true,
        ];

        yield 'jpg' => [
            'count' => 3,
            'filename' => 'french.jpg',
            'expectedStatus' => Response::HTTP_OK,
            'expectedAdded' => true,
        ];
    }

    /**
     * @dataProvider provideUploadImage
     */
    public function testUploadImage(int $count, string $filename, int $expectedStatus, bool $expectedAdded)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/content/upload?count='.$count, [
            'hidimg-'.$count => base64_encode(file_get_contents(__DIR__.'/../../../../Fixtures/upload/'.$filename)),
            'hidname-'.$count => 'file',
            'hidtype-'.$count => 'png',
        ]);

        $this->assertResponseStatusCodeSame($expectedStatus);

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertSame($expectedAdded, count($storage->listContents('.')->toArray()) > 0);
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-list-row'));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);
        $repository = static::getContainer()->get(PageRepository::class);
        $this->assertSame(2, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Delete');
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('.world-list-row'));
        $this->assertSame(1, $repository->count(['project' => $project->getId()]));
    }

    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-list-row'));

        $link = $crawler->filter('a:contains("New page")');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
    }

    public function provideUpdateMetadataInvalid()
    {
        yield 'invalid_description' => [str_repeat('x', 300), '[]'];
        yield 'invalid_categories' => ['Description', '[data: "xxx"]'];
    }

    /**
     * @dataProvider provideUpdateMetadataInvalid
     */
    public function testUpdateMetadataInvalidData(string $description, $categories)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/update/metadata',
            [
                'page' => [
                    'description' => $description,
                    'categories' => $categories,
                    'onlyForMembers' => '1',
                ],
            ],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateMetadataInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/update/metadata');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateImage()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $file = new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'm.png', 'image/png', null, true);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/update/image',
            [],
            ['page_image' => ['file' => $file]],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($payload = $client->getResponse()->getContent());

        // Check the database reference was saved
        /** @var Page $page */
        $page = static::getContainer()->get(PageRepository::class)->findOneBy(['uuid' => self::PAGE_THEORY_UUID]);
        $this->assertInstanceOf(Page::class, $page);
        $this->assertInstanceOf(Upload::class, $page->getImage());
        $this->assertStringEndsWith('/serve/'.$page->getImage()->getPathname(), Json::decode($payload)['image']);

        // Check the file was saved in the CDN
        $this->assertTrue(static::getContainer()->get('cdn.storage')->fileExists($page->getImage()->getPathname()));
    }

    public function provideCategories(): iterable
    {
        yield [
            'categories' => ['Health'],
        ];

        yield [
            'categories' => ['Health', 'Economy'],
        ];

        yield [
            'categories' => [],
        ];
    }

    /**
     * @dataProvider provideCategories
     */
    public function testUpdateCategories(array $categories)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $repo = static::getContainer()->get(PageCategoryRepository::class);

        $ids = [];
        foreach ($categories as $category) {
            $ids[] = $repo->findOneBy(['name' => $category])->getId();
        }

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/update/metadata',
            ['page' => ['categories' => Json::encode($ids)]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        $page = static::getContainer()->get(PageRepository::class)->findOneBy(['uuid' => self::PAGE_THEORY_UUID]);
        $this->assertCount(count($categories), $page->getCategories());
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages');
        $this->assertResponseIsSuccessful();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);
        $repository = static::getContainer()->get(PageRepository::class);
        $this->assertSame(2, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Duplicate');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(3, $repository->count(['project' => $project->getId()]));
    }

    public function testMove()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/move');
        $this->assertResponseIsSuccessful();

        /** @var Project $acmeProject */
        $acmeProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);

        $form = $crawler->selectButton('Move')->form();
        $client->submit($form, ['move_entity[into]' => $acmeProject->getId()]);

        // Check new location
        $page = static::getContainer()->get(PageRepository::class)->findOneBy(['uuid' => self::PAGE_THEORY_UUID]);
        $this->assertSame($acmeProject->getId(), $page->getProject()->getId());

        // Check redirect
        $location = $client->getResponse()->headers->get('Location');
        $this->assertSame('/console/project/'.self::PROJECT_ACME_UUID.'/website/pages', $location);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/'.self::PAGE_THEORY_UUID.'/view');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame(
            'https://citipo.com/_redirect/page/'.Uid::toBase62(Uuid::fromString(self::PAGE_THEORY_UUID)),
            $client->getResponse()->headers->get('Location')
        );
    }
}
