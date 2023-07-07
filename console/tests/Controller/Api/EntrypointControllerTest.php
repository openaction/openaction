<?php

namespace App\Tests\Controller\Api;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class EntrypointControllerTest extends ApiTestCase
{
    public function provideEntrypoint()
    {
        yield 'citipo' => [
            'token' => '748ea240b01970d6c9de708de7602e613adb4dd02aa084435088c8c5f806d9ad',
            'expected_status_code' => Response::HTTP_OK,
            'expected_project' => 'Citipo',
        ];

        yield 'ile-de-france' => [
            'token' => '3a4683898cdd75936c94475d55049c07c407b64f18e23d6f726894fc0cc79f4f',
            'expected_status_code' => Response::HTTP_OK,
            'expected_project' => 'Île-de-France',
        ];

        yield 'invalid' => [
            'token' => 'invalid',
            'expected_status_code' => Response::HTTP_UNAUTHORIZED,
            'expected_project' => null,
        ];

        yield 'anonymous' => [
            'token' => null,
            'expected_status_code' => Response::HTTP_UNAUTHORIZED,
            'expected_project' => null,
        ];
    }

    /**
     * @dataProvider provideEntrypoint
     */
    public function testEntrypoint(?string $token, int $expectedCode, ?string $expectedProject)
    {
        $client = self::createClient();

        $data = $this->apiRequest($client, 'GET', '/api', $token, $expectedCode);
        if ($expectedProject) {
            $this->assertStringContainsString($expectedProject, $data['message']);
        }
    }

    public function testEntrypointNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api', null, 401);
    }

    public function testEntrypointInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api', 'invalid', 401);
    }
}
