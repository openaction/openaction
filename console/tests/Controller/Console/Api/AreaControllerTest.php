<?php

namespace App\Tests\Controller\Console\Api;

use App\Repository\AreaRepository;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AreaControllerTest extends WebTestCase
{
    public function testForbiddenAnonymous()
    {
        $client = static::createClient();
        $client->request('GET', '/console/api/areas/search');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testAccessRoot()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/api/areas/search');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('France', $client->getResponse()->getContent());
    }

    public function testAccessChildren()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $idf = static::getContainer()->get(AreaRepository::class)->findOneBy(['code' => 'fr_11']);

        $client->request('GET', '/console/api/areas/search?p='.$idf->getId());
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Hauts-de-Seine', $client->getResponse()->getContent());
    }
}
