<?php

namespace App\Tests\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\DomCrawler\Crawler;

class IndexControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage');

        $this->assertResponseIsSuccessful();
        $this->assertCount(8, $crawler->filter('.appearance-homepage-section'));
    }

    public function testSort()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Fetch current order
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage');
        $this->assertResponseIsSuccessful();
        $this->assertCount(6, $ids = array_values(array_filter($crawler->filter('.appearance-homepage-section')->each(fn (Crawler $row) => $row->attr('data-id')))));

        // Reverse the order
        $token = $this->filterGlobalCsrfToken($crawler);

        $payload = [];
        $i = 1;
        foreach (array_reverse($ids) as $id) {
            $payload[] = ['id' => (int) $id, 'order' => $i];
            ++$i;
        }

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage/sort?_token='.$token,
            ['data' => Json::encode($payload)]
        );

        $this->assertResponseIsSuccessful();

        // Check order was reversed (use the last list, ie the footer, as the header has submenus and that messes with the order)
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage');
        $this->assertResponseIsSuccessful();
        $this->assertCount(6, $newIds = array_values(array_filter($crawler->filter('.appearance-homepage-section')->each(fn (Crawler $row) => $row->attr('data-id')))));
        $this->assertSame(array_reverse($ids), $newIds);
    }
}
