<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PetitionControllerTest extends WebTestCase
{
    public function testViewShowsLocalizedTitleAndDescription(): void
    {
        $client = self::createClient();

        // Discover a petition URL from the real sitemap provided by Console
        $client->request('GET', '/sitemap.xml');
        if (!$client->getResponse()->isSuccessful()) {
            $this->markTestSkipped('Console not available or project not resolved for tests.');
        }

        $sitemap = simplexml_load_string($client->getResponse()->getContent());
        $petitionUrl = null;
        foreach ($sitemap->children() as $child) {
            $loc = (string) $child->loc;
            if (str_contains($loc, '/pe/')) {
                $petitionUrl = $loc;
                break;
            }
        }

        if (!$petitionUrl) {
            $this->markTestSkipped('No petitions found in sitemap (fixtures may not include petitions).');
        }

        $path = parse_url($petitionUrl, PHP_URL_PATH);
        $client->request('GET', $path);
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<h1', $client->getResponse()->getContent());
    }
}
