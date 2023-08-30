<?php

namespace App\Tests\Controller\Console\Api;

use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class TagControllerTest extends WebTestCase
{
    public function testForbiddenAnonymous()
    {
        $client = static::createClient();
        $client->request('GET', '/console/api/tags/search');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testForbiddenOrganizationNotMember()
    {
        $client = static::createClient();
        $this->authenticate($client, 'ema.anderson@away.com');

        $client->request('GET', '/console/api/tags/search?o=219025aa-7fe2-4385-ad8f-31f386720d10&q=example');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function provideSearch()
    {
        yield 'no term' => [
            'query' => '',
            'expectedResults' => [
                // Order by name if no term provided
                'ContainsTagInside',
                'contains tag keyword lowercase',
                'DontStartWithTag',
                'ExampleTag',
                'StartWithTag',
                'Tag',
                'tag start with keyword lowercase',
            ],
        ];

        yield 'exact' => [
            'query' => 'Tag',
            'expectedResults' => [
                'Tag',
                'tag start with keyword lowercase',
                'ContainsTagInside',
                'contains tag keyword lowercase',
                'DontStartWithTag',
                'ExampleTag',
                'StartWithTag',
            ],
        ];

        yield 'starts with' => [
            'query' => 'Start',
            'expectedResults' => [
                'StartWithTag',
                'DontStartWithTag',
                'tag start with keyword lowercase',
            ],
        ];
    }

    /**
     * @dataProvider provideSearch
     */
    public function testSearch(string $query, array $expectedResults)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/api/tags/search?o=219025aa-7fe2-4385-ad8f-31f386720d10&q='.$query);
        $this->assertResponseIsSuccessful();
        $this->assertSame($expectedResults, array_column(Json::decode($client->getResponse()->getContent()), 'name'));
    }
}
