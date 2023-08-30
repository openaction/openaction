<?php

namespace App\Tests\Controller\Subscription;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExpiredControllerTest extends WebTestCase
{
    public function provideTemporarilyRestrictedUrls(): iterable
    {
        yield ['/console/organization/941ab5a3-2af6-4405-84ba-a1d2ad80292b/projects'];
        yield ['/console/organization/941ab5a3-2af6-4405-84ba-a1d2ad80292b/project/create'];
        yield ['/console/organization/941ab5a3-2af6-4405-84ba-a1d2ad80292b/team'];
        yield ['/console/project/5767c01d-e6c1-4a29-a1d3-194ccd14a93f/start'];
        yield ['/console/project/5767c01d-e6c1-4a29-a1d3-194ccd14a93f/website/posts'];
        yield ['/console/project/5767c01d-e6c1-4a29-a1d3-194ccd14a93f/website/pages'];
        yield ['/console/project/5767c01d-e6c1-4a29-a1d3-194ccd14a93f/website/documents'];
    }

    /**
     * @dataProvider provideTemporarilyRestrictedUrls
     */
    public function testTemporarilyRestrictedUrls(string $url): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'arianneverreau@example.com');

        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_PAYMENT_REQUIRED);
    }

    public function provideAccessibleUrls(): iterable
    {
        yield ['/console/user/account'];
        yield ['/console/user/password/update'];
        yield ['/console/user/two-factor'];
        yield ['/console/user/notification/update'];
    }

    /**
     * @dataProvider provideAccessibleUrls
     */
    public function testAccessibleUrls(string $url): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'arianneverreau@example.com');

        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }
}
