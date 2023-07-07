<?php

namespace App\Tests\Controller\Console;

use App\Tests\WebTestCase;

class OrganizationControllerTest extends WebTestCase
{
    public function testSwitch()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/projects');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('[data-orga]:contains("Citipo")'));
        $this->assertCount(1, $crawler->filter('[data-credits-emails]:contains("1,000,000")'));
        $this->assertCount(1, $crawler->filter('[data-credits-texts]:contains("10")'));

        $orgas = [];

        /** @var \DOMNode $node */
        foreach ($crawler->filter('[data-orga-chooser-item]') as $node) {
            $orgas[] = trim($node->textContent);
        }

        // Check number, value and order of the list
        $this->assertSame(['Acme', 'Citipo', 'Essential', 'Example Co', 'Premium', 'Standard'], $orgas);

        $crawler = $client->click($crawler->filter('[data-orga-chooser-item]:contains("Acme")')->link());
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('[data-orga]:contains("Acme")'));
    }
}
