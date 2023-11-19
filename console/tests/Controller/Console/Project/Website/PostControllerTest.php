<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\Post;
use App\Repository\ProjectRepository;
use App\Repository\Website\PostCategoryRepository;
use App\Repository\Website\PostRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Util\Uid;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class PostControllerTest extends WebTestCase
{
    private const POST_GRAVITATION_UUID = '53aba31d-f8bb-483d-a5dd-2926a1d2265e';

    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts');
        $this->assertResponseIsSuccessful();

        $this->assertCount(2, $crawler->filter('.world-list-row'));
        $this->assertStringEndsWith('/res/images/default.jpg', $crawler->selectImage('Gravitation')->image()->getUri());
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#post-editor');
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/content',
            ['post' => ['title' => $title, 'content' => $content]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['uuid' => self::POST_GRAVITATION_UUID]);
        $this->assertEquals($title, $post->getTitle());
        $this->assertEquals($content, $post->getContent());
    }

    public function providUpdateContentInvalid()
    {
        yield 'title_empty' => ['', '', ''];
        yield 'title_invalid_length' => [str_repeat('x', 300), '', ''];
    }

    /**
     * @dataProvider providUpdateContentInvalid
     */
    public function testUpdateContentInvalidData(string $title, string $content)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/content',
            ['post' => ['title' => $title, 'content' => $content]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateContentInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/content');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function provideUpdateMetadata(): iterable
    {
        yield 'youtube' => [
            'description',
            'https://www.youtube.com/watch?v=rjb9FdVdX5I',
            'youtube:rjb9FdVdX5I',
            'My quote',
            '1',
            '',
        ];

        yield 'facebook' => [
            'description',
            'https://www.facebook.com/watch/?v=427923618291453',
            'facebook:427923618291453',
            'My quote',
            '0',
            '',
        ];

        yield 'no_video' => [
            'description',
            '',
            null,
            '',
            '1',
            'https://google.com',
        ];
    }

    /**
     * @dataProvider provideUpdateMetadata
     */
    public function testUpdateMetadata(
        string $description,
        string $video,
        ?string $expectedVideo,
        string $quote,
        string $onlyForMembers,
        ?string $externalUrl,
    ) {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/metadata',
            [
                'post' => [
                    'description' => $description,
                    'video' => $video,
                    'quote' => $quote,
                    'externalUrl' => $externalUrl,
                    'onlyForMembers' => $onlyForMembers,
                ],
            ],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var Post $post */
        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['uuid' => self::POST_GRAVITATION_UUID]);
        $this->assertEquals($description, $post->getDescription());
        $this->assertEquals($expectedVideo, $post->getVideo());
        $this->assertEquals($quote, $post->getQuote());
        $this->assertSame($externalUrl ?: null, $post->getExternalUrl());
        $this->assertSame((bool) $onlyForMembers, $post->isOnlyForMembers());
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

        $client->request('POST', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/content/upload?count='.$count, [
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
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-list-row'));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);
        $repository = static::getContainer()->get(PostRepository::class);
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
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-list-row'));

        $link = $crawler->filter('a:contains("New post")');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
    }

    public function provideUpdateMetadataInvalid()
    {
        yield 'description_invalid_length' => [str_repeat('x', 300), '', ''];
        yield 'quote_invalid_length' => ['Title', '', str_repeat('x', 300)];
    }

    /**
     * @dataProvider provideUpdateMetadataInvalid
     */
    public function testUpdateMetadataInvalidData(string $description, string $video, string $quote)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/metadata',
            [
                'post' => [
                    'description' => $description,
                    'video' => $video,
                    'quote' => $quote,
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

        $client->request('POST', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/metadata');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateImage()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $file = new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'm.png', 'image/png', null, true);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/image',
            [],
            ['post_image' => ['file' => $file]],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($payload = $client->getResponse()->getContent());

        // Check the database reference was saved
        /** @var Post $post */
        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['uuid' => self::POST_GRAVITATION_UUID]);
        $this->assertInstanceOf(Post::class, $post);
        $this->assertInstanceOf(Upload::class, $post->getImage());
        $this->assertStringEndsWith('/serve/'.$post->getImage()->getPathname(), Json::decode($payload)['image']);

        // Check the file was saved in the CDN
        $this->assertTrue(static::getContainer()->get('cdn.storage')->fileExists($post->getImage()->getPathname()));
    }

    public function provideCategories(): iterable
    {
        yield [
            'categories' => ['Programme'],
        ];

        yield [
            'categories' => ['Programme', 'Communiqués de presse'],
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $repo = static::getContainer()->get(PostCategoryRepository::class);

        $ids = [];
        foreach ($categories as $category) {
            $ids[] = $repo->findOneBy(['name' => $category])->getId();
        }

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/metadata',
            ['post' => ['categories' => Json::encode($ids)]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['uuid' => self::POST_GRAVITATION_UUID]);
        $this->assertCount(count($categories), $post->getCategories());
    }

    public function provideAuthors(): iterable
    {
        yield [
            'authors' => ['Nathalie Loiseau'],
        ];

        yield [
            'authors' => ['Nathalie Loiseau', 'Pascal Canfin'],
        ];

        yield [
            'authors' => [],
        ];
    }

    /**
     * @dataProvider provideAuthors
     */
    public function testUpdateAuthors(array $authors)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $repo = static::getContainer()->get(TrombinoscopePersonRepository::class);

        $ids = [];
        foreach ($authors as $author) {
            $ids[] = $repo->findOneBy(['fullName' => $author])->getId();
        }

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/update/metadata',
            ['post' => ['authors' => Json::encode($ids)]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['uuid' => self::POST_GRAVITATION_UUID]);
        $this->assertCount(count($authors), $post->getAuthors());
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts');
        $this->assertResponseIsSuccessful();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);
        $repository = static::getContainer()->get(PostRepository::class);
        $this->assertSame(2, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Duplicate');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(3, $repository->count(['project' => $project->getId()]));
    }

    public function testMove()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/move');
        $this->assertResponseIsSuccessful();

        /** @var Project $acmeProject */
        $acmeProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);

        $form = $crawler->selectButton('Move')->form();
        $client->submit($form, ['move_entity[into]' => $acmeProject->getId()]);

        // Check new location
        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['uuid' => self::POST_GRAVITATION_UUID]);
        $this->assertSame($acmeProject->getId(), $post->getProject()->getId());

        // Check redirect
        $location = $client->getResponse()->headers->get('Location');
        $this->assertSame('/console/project/'.self::PROJECT_ACME_UUID.'/website/posts', $location);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testCrosspost()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/crosspost');
        $this->assertResponseIsSuccessful();

        /** @var Project $acmeProject */
        $acmeProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);

        $form = $crawler->selectButton('Crosspost')->form();
        $client->submit($form, ['crosspost_entity[intoProjects]' => [$acmeProject->getId()]]);

        // Check original location didn't change
        /** @var Post $post */
        $post = static::getContainer()->get(PostRepository::class)->findOneBy(['uuid' => self::POST_GRAVITATION_UUID]);
        $this->assertNotSame($acmeProject->getId(), $post->getProject()->getId());

        // Check duplicate location is target project
        /** @var Post $duplicate */
        $duplicate = static::getContainer()->get(PostRepository::class)->findOneBy([
            'title' => $post->getTitle(),
            'project' => $acmeProject,
        ]);
        $this->assertInstanceOf(Post::class, $duplicate);
        $this->assertNotSame($post->getId(), $duplicate->getId());
        $this->assertSame($post->getTitle(), $duplicate->getTitle());
        $this->assertSame($post->getContent(), $duplicate->getContent());
        $this->assertSame($post->getDescription(), $duplicate->getDescription());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/'.self::POST_GRAVITATION_UUID.'/view');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame(
            'https://citipo.com/_redirect/post/'.Uid::toBase62(Uuid::fromString(self::POST_GRAVITATION_UUID)),
            $client->getResponse()->headers->get('Location')
        );
    }
}
