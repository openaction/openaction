<?php

namespace App\Tests\Controller\Api\Admin;

use App\Tests\ApiTestCase;
use App\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class RedirectionControllerTest extends ApiTestCase
{
    public function testCreate(): void
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/admin/redirections',
            token: self::EXAMPLECO_ADMIN_TOKEN,
            expectedStatusCode: Response::HTTP_CREATED,
            content: Json::encode([
                'fromUrl' => 'https://localhost/from-url',
                'toUrl' => 'https://localhost/to-url',
                'type' => 301,
            ]),
        );

        $this->assertApiResponse($result, [
            'fromUrl' => 'https://localhost/from-url',
            'toUrl' => 'https://localhost/to-url',
            'type' => 301,
        ]);
    }
}
