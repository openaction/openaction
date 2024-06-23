<?php

namespace App\Tests\Analytics\Consumer;

use App\Analytics\Consumer\RefreshContactStatsHandler;
use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Entity\Organization;
use App\Entity\Project;
use App\Repository\Analytics\Community\ContactCreationRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use App\Tests\KernelTestCase;

class RefreshContactsStatsHandlerTest extends KernelTestCase
{
    public function testConsume()
    {
        self::bootKernel();

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $this->assertInstanceOf(Organization::class, $orga);

        /** @var Project $globalProject */
        $globalProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('e816bcc6-0568-46d1-b0c5-917ce4810a87');
        $this->assertInstanceOf(Project::class, $globalProject);

        /** @var Project $localProject */
        $localProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('151f1340-9ad6-47c7-a8a5-838ff955eae7');
        $this->assertInstanceOf(Project::class, $localProject);

        /** @var Project $thematicProject */
        $thematicProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('062d7a3b-7cf3-48b0-b905-21f09844fb81');
        $this->assertInstanceOf(Project::class, $thematicProject);

        // Check current contact stats
        $this->assertSame(2, static::getContainer()->get(ContactCreationRepository::class)->count(['project' => $globalProject]));
        $this->assertSame(1, static::getContainer()->get(ContactCreationRepository::class)->count(['project' => $localProject]));
        $this->assertSame(0, static::getContainer()->get(ContactCreationRepository::class)->count(['project' => $thematicProject]));

        /** @var RefreshContactStatsHandler $handler */
        $handler = static::getContainer()->get(RefreshContactStatsHandler::class);
        $handler(new RefreshContactStatsMessage($orga->getId()));

        // Check contact stats were updated
        $this->assertSame(6, static::getContainer()->get(ContactCreationRepository::class)->count(['project' => $globalProject]));
        $this->assertSame(5, static::getContainer()->get(ContactCreationRepository::class)->count(['project' => $localProject]));
        $this->assertSame(4, static::getContainer()->get(ContactCreationRepository::class)->count(['project' => $thematicProject]));
    }
}
