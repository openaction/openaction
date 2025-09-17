<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Tests\WebTestCase;

class PetitionControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();

        // Should list petitions from fixtures
        $this->assertCount(1, $crawler->filter('.world-list-row'));
        $this->assertStringContainsString('Save the Park', $crawler->filter('.petitions-title')->text());
    }

    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Open list to retrieve CSRF token and follow create
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();
        $token = $this->filterGlobalCsrfToken($crawler);

        $createUrl = '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/create?_token='.$token;
        $client->request('GET', $createUrl);
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Should be on the localized edit page with an editor title input
        $this->assertSelectorExists('input#petition-title');

        // Locale of new localized petition should match request locale (default test kernel is en)
        $this->assertStringContainsString('Edit petition (en)', $crawler->filter('title')->text('')); // page_title block
    }
}
