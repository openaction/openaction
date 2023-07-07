<?php

namespace App\Tests\Controller\Console\Project\Developers;

use App\Tests\WebTestCase;

class AccessControllerTest extends WebTestCase
{
    public function testSeeApiKeys()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/access');
        $this->assertResponseIsSuccessful();
        $this->assertSame('748ea240b01970d6c9de708de7602e613adb4dd02aa084435088c8c5f806d9ad', $crawler->filter('[data-token]')->attr('value'));
    }
}
