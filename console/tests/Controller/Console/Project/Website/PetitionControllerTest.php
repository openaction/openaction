<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Repository\ProjectRepository;
use App\Repository\Website\PetitionRepository;
use App\Tests\WebTestCase;

class PetitionControllerTest extends WebTestCase
{
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

        // Click the first Delete link
        $link = $crawler->selectLink('Delete')->link();
        $client->click($link);
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $countAfter = $repository->count(['project' => $project->getId()]);
        $this->assertSame($countBefore - 1, $countAfter);
    }
}
