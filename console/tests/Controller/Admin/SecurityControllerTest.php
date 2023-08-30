<?php

namespace App\Tests\Controller\Admin;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function provideAdminAccessSecurity()
    {
        yield 'admin' => [
            'email' => 'titouan.galopin@citipo.com',
            'expected_status_code' => Response::HTTP_MOVED_PERMANENTLY,
            'expected_redirect' => 'http://localhost/admin',
        ];

        yield 'user' => [
            'email' => 'ema.anderson@away.com',
            'expected_status_code' => Response::HTTP_FORBIDDEN,
            'expected_redirect' => null,
        ];

        yield 'anonymous' => [
            'email' => null,
            'expected_status_code' => Response::HTTP_FOUND,
            'expected_redirect' => '/security/login',
        ];
    }

    /**
     * @dataProvider provideAdminAccessSecurity
     */
    public function testAdminAccessSecurity(?string $email, int $expectedCode, ?string $expectedRedirect)
    {
        $client = static::createClient();

        if ($email) {
            $this->authenticate($client, $email);
        }

        $client->request('GET', '/admin/');
        $this->assertResponseStatusCodeSame($expectedCode);

        if ($expectedRedirect) {
            $location = $client->getResponse()->headers->get('Location');
            $this->assertStringStartsWith($expectedRedirect, $location);
        }
    }
}
