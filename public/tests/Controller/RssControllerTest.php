<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RssControllerTest extends WebTestCase
{
    public function testRssFeed()
    {
        $client = self::createClient();
        $client->request('GET', '/rss.xml');
        $this->assertResponseIsSuccessful();

        $feed = simplexml_load_string($client->getResponse()->getContent());
        $this->assertInstanceOf(\SimpleXMLElement::class, $feed);
    }
}
