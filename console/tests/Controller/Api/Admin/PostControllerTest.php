<?php

namespace App\Tests\Controller\Api\Admin;

use App\Tests\ApiTestCase;
use App\Util\Json;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends ApiTestCase
{
    public function testCreateFull(): void
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/admin/posts',
            token: self::EXAMPLECO_ADMIN_TOKEN,
            expectedStatusCode: Response::HTTP_CREATED,
            content: Json::encode([
                'title' => 'Title example',
                'content' => '<div class="row"><div class="col-md-12"><p>Content example</p></div></div>',
                'description' => 'Description example',
                'quote' => 'Quote example',
                'videoUrl' => 'https://www.youtube.com/watch?v=muDpH2Ty2tg',
                'publishedAt' => '2023-02-25 10:00:00',
                'categories' => ['Category 2', 'Category to create'],
            ]),
        );

        $this->assertApiResponse($result, [
            '_resource' => 'Post',
            '_links' => [
                'self' => 'http://localhost/api/website/posts/'.$result['id'],
            ],
            'id' => $result['id'],
            'title' => 'Title example',
            'quote' => 'Quote example',
            'slug' => 'title-example',
            'description' => 'Description example',
            'video' => 'youtube:muDpH2Ty2tg',
            'image' => null,
            'sharer' => null,
            'categories' => [
                'data' => [
                    [
                        '_links' => [
                            'self' => 'http://localhost/api/website/posts-categories/1GmkaorS3YSezgfKGrZel1',
                        ],
                        'id' => '1GmkaorS3YSezgfKGrZel1',
                        'name' => 'Category 2',
                        'slug' => 'category-2',
                    ],
                    [
                        '_links' => [
                            'self' => 'http://localhost/api/website/posts-categories/'.$result['categories']['data'][1]['id'],
                        ],
                        'id' => $result['categories']['data'][1]['id'],
                        'name' => 'Category to create',
                        'slug' => 'category-to-create',
                    ],
                ],
            ],
            'authors' => [
                'data' => [],
            ],
        ]);
    }

    public function testCreateMinimal(): void
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/admin/posts',
            token: self::EXAMPLECO_ADMIN_TOKEN,
            expectedStatusCode: Response::HTTP_CREATED,
            content: Json::encode([
                'title' => 'Title example',
            ]),
        );

        $this->assertApiResponse($result, [
            '_resource' => 'Post',
            '_links' => [
                'self' => 'http://localhost/api/website/posts/'.$result['id'],
            ],
            'id' => $result['id'],
            'title' => 'Title example',
            'quote' => null,
            'slug' => 'title-example',
            'description' => null,
            'video' => null,
            'image' => null,
            'sharer' => null,
            'categories' => [
                'data' => [],
            ],
            'authors' => [
                'data' => [],
            ],
        ]);
    }

    public function testUploadMainImage(): void
    {
        $response = $this->createApiRequest('PUT', '/api/admin/posts/5dekUWIC8GW8BndaybQ3Yj/main-image')
            ->withApiToken(self::EXAMPLECO_ADMIN_TOKEN)
            ->withFile('file', new UploadedFile(__DIR__.'/../../../Fixtures/upload/image.webp', 'image.webp', 'image/webp'))
            ->send()
        ;

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED, $response);

        $result = Json::decode($response->getContent());
        $this->assertSame('5dekUWIC8GW8BndaybQ3Yj', $result['id']);
        $this->assertNotNull($result['image']);
        $this->assertNotNull($result['sharer']);

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertCount(1, $storage->listContents('.')->toArray());
    }
}
