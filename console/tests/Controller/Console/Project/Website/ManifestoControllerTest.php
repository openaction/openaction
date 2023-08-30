<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Tests\WebTestCase;

class ManifestoControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();

        $this->assertCount(4, $titleNodes = $crawler->filter('.world-list-row .topics-title'));

        $titles = [];
        foreach ($titleNodes as $node) {
            $titles[] = trim($node->textContent);
        }

        $expected = [
            'Pour une ville plus durable',
            'Pour une ville plus sûre',
            'Pour une ville plus tranquille',
            'Not published',
        ];

        $this->assertSame($expected, $titles);
    }
}
