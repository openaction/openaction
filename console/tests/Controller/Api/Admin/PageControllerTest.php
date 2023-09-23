<?php

namespace App\Tests\Controller\Api\Admin;

use App\Tests\ApiTestCase;
use App\Util\Json;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends ApiTestCase
{
    public function testCreateFull(): void
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/admin/pages',
            token: self::EXAMPLECO_ADMIN_TOKEN,
            expectedStatusCode: Response::HTTP_CREATED,
            content: Json::encode([
                'title' => 'Title example',
                'content' => '<div class="row"><div class="col-md-12"><p>Content example</p></div></div>',
                'description' => 'Description example',
                'categories' => ['Category 2', 'Category to create'],
            ]),
        );

        // Test the payload is the one expected, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Page',
            '_links' => [
                'self' => 'http://localhost/api/website/pages/'.$result['id'],
            ],
            'id' => $result['id'],
            'title' => 'Title example',
            'slug' => 'title-example',
            'description' => 'Description example',
            'image' => null,
            'sharer' => null,
            'categories' => [
                'data' => [
                    [
                        '_links' => [
                            'self' => 'http://localhost/api/website/pages-categories/7hIQY74GJcZWKsJxafwbHZ',
                        ],
                        'id' => '7hIQY74GJcZWKsJxafwbHZ',
                        'name' => 'Category 2',
                        'slug' => 'category-2',
                    ],
                    [
                        '_links' => [
                            'self' => 'http://localhost/api/website/pages-categories/'.$result['categories']['data'][1]['id'],
                        ],
                        'id' => $result['categories']['data'][1]['id'],
                        'name' => 'Category to create',
                        'slug' => 'category-to-create',
                    ],
                ],
            ],
        ]);
    }

    public function testCreateMinimal(): void
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/admin/pages',
            token: self::EXAMPLECO_ADMIN_TOKEN,
            expectedStatusCode: Response::HTTP_CREATED,
            content: Json::encode([
                'title' => 'Title example',
            ]),
        );

        $this->assertApiResponse($result, [
            '_resource' => 'Page',
            '_links' => [
                'self' => 'http://localhost/api/website/pages/'.$result['id'],
            ],
            'id' => $result['id'],
            'title' => 'Title example',
            'slug' => 'title-example',
            'description' => null,
            'image' => null,
            'sharer' => null,
            'categories' => [
                'data' => [],
            ],
        ]);
    }

    public function testUploadMainImage(): void
    {
        $response = $this->createApiRequest('PUT', '/api/admin/pages/64dhFZ31PdPTzL5fS91hRj/main-image')
            ->withApiToken(self::EXAMPLECO_ADMIN_TOKEN)
            ->withFile('file', new UploadedFile(__DIR__.'/../../../Fixtures/upload/image.webp', 'image.webp', 'image/webp'))
            ->send()
        ;

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED, $response);

        $result = Json::decode($response->getContent());
        $this->assertSame('64dhFZ31PdPTzL5fS91hRj', $result['id']);
        $this->assertNotNull($result['image']);
        $this->assertNotNull($result['sharer']);

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertCount(1, $storage->listContents('.')->toArray());
    }
}
