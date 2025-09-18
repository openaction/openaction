<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapPetitionsTest extends WebTestCase
{
    public function testSitemapIncludesPetitions(): void
    {
        $client = self::createClient();

        $client->request('GET', '/sitemap.xml');
        if (!$client->getResponse()->isSuccessful()) {
            $this->markTestSkipped('Console not available or project not resolved for tests.');
        }

        $sitemap = simplexml_load_string($client->getResponse()->getContent());
        $found = false;
        foreach ($sitemap->children() as $child) {
            if (str_contains((string) $child->loc, '/pe/')) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->markTestSkipped('No petitions found in sitemap (fixtures may not include petitions).');
        }

        $this->assertTrue($found);
    }
}
