<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test the sitemap and all URLs inside it.
 */
class SmokeTestControllerTest extends WebTestCase
{
    public function testSmokeSitemapUrls()
    {
        $client = self::createClient();
        $client->request('GET', '/sitemap.xml');

        $sitemap = simplexml_load_string($client->getResponse()->getContent());
        $this->assertInstanceOf(\SimpleXMLElement::class, $sitemap);

        foreach ($sitemap->children() as $child) {
            $client->request('GET', (string) $child->loc);
            $this->assertResponseIsSuccessful('URL '.$child->loc.' should be accessible');
        }
    }
}
