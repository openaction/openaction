<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Repository\ProjectRepository;
use App\Repository\Website\PetitionRepository;
use App\Tests\WebTestCase;

class PetitionControllerTest extends WebTestCase
{
    public function testList(): void
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();
        $this->assertGreaterThanOrEqual(1, $crawler->filter('.world-list-row')->count());
    }

    public function testCreate(): void
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();

        $link = $crawler->selectLink('Create petition');
        $this->assertGreaterThan(0, $link->count());
        $client->click($link->link());

        $this->assertResponseStatusCodeSame(302);
        $this->assertMatchesRegularExpression('~^/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/[0-9a-zA-Z\-]+/edit$~', $client->getResponse()->headers->get('Location'));
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();
        $this->assertGreaterThanOrEqual(1, $crawler->filter('.world-list-row')->count());

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);
        $repository = static::getContainer()->get(PetitionRepository::class);
        $countBefore = $repository->count(['project' => $project->getId()]);
        $this->assertGreaterThanOrEqual(1, $countBefore);

        // Click the parent Delete button (not localized delete)
        $deleteButton = $crawler->filter('a.petitions-list-action-delete')->first();
        $client->click($deleteButton->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $countAfter = $repository->count(['project' => $project->getId()]);
        $this->assertSame($countBefore - 1, $countAfter);
    }
}
