<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ViewControllerTest extends WebTestCase
{
    public function testViewHome()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/view');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame('https://citipo.com/_redirect/home', $client->getResponse()->headers->get('Location'));
    }
}
