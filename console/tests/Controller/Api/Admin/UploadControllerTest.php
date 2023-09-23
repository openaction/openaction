<?php

namespace App\Tests\Controller\Api\Admin;

use App\Tests\ApiTestCase;
use App\Util\Json;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class UploadControllerTest extends ApiTestCase
{
    public function testCreate(): void
    {
        $response = $this->createApiRequest('POST', '/api/admin/uploads')
            ->withApiToken(self::EXAMPLECO_ADMIN_TOKEN)
            ->withFile('file', new UploadedFile(__DIR__.'/../../../Fixtures/upload/image.webp', 'image.webp', 'image/webp'))
            ->send()
        ;

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED, $response);
        $this->assertNotNull(Json::decode($response->getContent())['url'] ?? null);

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertCount(1, $storage->listContents('.')->toArray());
    }
}
