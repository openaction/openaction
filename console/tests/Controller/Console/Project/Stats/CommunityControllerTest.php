<?php

namespace App\Tests\Controller\Console\Project\Stats;

use App\Tests\WebTestCase;

class CommunityControllerTest extends WebTestCase
{
    public function testIndexPeriods()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/stats/community');
        $this->assertResponseIsSuccessful();
        $this->assertSame(6, (int) trim($crawler->filter('[data-total="contacts"]')->text()));
        $this->assertSame(3, (int) trim($crawler->filter('[data-total="members"]')->text()));
        $this->assertSame(5, (int) trim($crawler->filter('[data-total="newsletter_subscribers"]')->text()));
        $this->assertSame(5, (int) trim($crawler->filter('[data-total="sms_subscribers"]')->text()));
    }
}
