<?php

namespace App\Tests\Controller\Console\Project\Community\Printing;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing');
        $this->assertResponseIsSuccessful();
        $this->assertCount(9, $crawler->filter('a:contains("Supprimer")'));
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/8aa012a7-cbf5-4ca5-ab67-fcf338c7309a/view');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists(':contains("Paiement")');
        $this->assertSelectorExists(':contains("Bon à tirer")');
        $this->assertSelectorExists(':contains("Livraison")');
        $this->assertSelectorExists(':contains("Vos informations")');
    }

    public function testProductDelete()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('.print-order-product a:contains("Supprimer")');
        $this->assertCount(5, $link);

        $client->click($link->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(4, $crawler->filter('.print-order-product a:contains("Supprimer")'));
    }

    public function testProductDeleteInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('.print-order-product a:contains("Supprimer")');
        $this->assertCount(5, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing');
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $crawler->filter('.print-order-product a:contains("Supprimer")'));
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('.print-order-reference a:contains("Supprimer")');
        $this->assertCount(9, $link);

        $client->click($link->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('.print-order-reference a:contains("Supprimer")'));
    }

    public function testDeleteInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('.print-order-reference a:contains("Supprimer")');
        $this->assertCount(9, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing');
        $this->assertResponseIsSuccessful();
        $this->assertCount(9, $crawler->filter('.print-order-reference a:contains("Supprimer")'));
    }
}
