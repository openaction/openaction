<?php

namespace App\Tests\Controller\Console\Project\Stats;

use App\Tests\WebTestCase;
use App\Util\Json;

class TrafficControllerTest extends WebTestCase
{
    public function provideIndexPeriods()
    {
        yield '1d' => [
            'period' => '1d',
            'expectedUsers' => 2,
            'expectedPageViews' => 2,
        ];

        yield '7d' => [
            'period' => '7d',
            'expectedUsers' => 19,
            'expectedPageViews' => 44,
        ];

        yield 'default' => [
            'period' => '',
            'expectedUsers' => 189,
            'expectedPageViews' => 392,
        ];

        yield '30d' => [
            'period' => '30d',
            'expectedUsers' => 189,
            'expectedPageViews' => 392,
        ];

        yield '90d' => [
            'period' => '90d',
            'expectedUsers' => 189,
            'expectedPageViews' => 392,
        ];

        yield '1y' => [
            'period' => '1y',
            'expectedUsers' => 189,
            'expectedPageViews' => 392,
        ];
    }

    /**
     * @dataProvider provideIndexPeriods
     */
    public function testIndexPeriods(string $period, int $expectedUsers, int $expectedPageViews)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/stats/traffic?period='.$period);
        $this->assertResponseIsSuccessful();
        $this->assertSame($expectedUsers, (int) trim($crawler->filter('[data-total="users"]')->text()));
        $this->assertSame($expectedPageViews, (int) trim($crawler->filter('[data-total="page_views"]')->text()));
    }

    public function testLive()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/stats/traffic/live');
        $this->assertResponseIsSuccessful();
        $this->assertSame('application/json', $client->getResponse()->headers->get('Content-Type'));
        $this->assertSame(['count' => 0], Json::decode($client->getResponse()->getContent()));
    }
}
