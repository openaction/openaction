<?php

namespace App\Tests\Listener;

use App\Tests\WebTestCase;

class ExposeCurrentUserToApmListenerTest extends WebTestCase
{
    public function testAnonymous()
    {
        $client = static::createClient();

        $client->request('GET', '/security/login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('X-Current-User-Type', 'user');
        $this->assertResponseHeaderSame('X-Current-User-Id', 'anonymous');
    }

    public function testUser()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/projects');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('X-Current-User-Type', 'user');
        $this->assertResponseHeaderSame('X-Current-User-Id', self::USER_TGALOPIN_UUID);
    }

    public function testProject()
    {
        $client = static::createClient();

        $client->request('GET', '/api/project', [], [], [
            'HTTP_ACCEPT' => 'application/ld+json',
            'HTTP_AUTHORIZATION' => 'Bearer 41d7821176ed9079640650922e1290aba97b949362339a7ed5539f0d5b9f21ba',
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('X-Current-User-Type', 'project');
        $this->assertResponseHeaderSame('X-Current-User-Id', self::PROJECT_EXAMPLECO_UUID);
    }
}
