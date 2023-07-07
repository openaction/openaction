<?php

namespace App\Tests\Controller\Console\Api;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CsrfTokenControllerTest extends WebTestCase
{
    public function testForbiddenAnonymous()
    {
        $client = static::createClient();
        $client->request('GET', '/console/api/csrf-token/refresh');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testRefresh()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/api/csrf-token/refresh');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('token', $client->getResponse()->getContent());
        $this->assertJson($client->getResponse()->getContent());
    }
}
