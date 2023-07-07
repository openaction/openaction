<?php

namespace App\Tests\Controller\Console\Api;

use App\Repository\Platform\JobRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class JobControllerTest extends WebTestCase
{
    public function testGetStatusForbiddenAnonymous()
    {
        $client = static::createClient();
        $client->request('GET', '/console/api/jobs/1');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testGetStatusSuccess()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $job = static::getContainer()->get(JobRepository::class)->findOneBy([], []);
        $client->request('GET', '/console/api/jobs/'.$job->getId());
        $this->assertResponseIsSuccessful();
        $this->assertSame(
            [
                'finished' => true,
                'step' => 1,
                'progress' => 1,
                'payload' => ['key' => 'value'],
            ],
            Json::decode($client->getResponse()->getContent())
        );
    }
}
