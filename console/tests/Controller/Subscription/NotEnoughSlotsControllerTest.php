<?php

namespace App\Tests\Controller\Subscription;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class NotEnoughSlotsControllerTest extends WebTestCase
{
    public function testTemporarilyRestrictedUrls()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/project/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_PAYMENT_REQUIRED);
    }
}
