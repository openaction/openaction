<?php

namespace App\Tests\Analytics\Consumer;

use App\Analytics\Consumer\ClearWebsiteSessionsHandler;
use App\Analytics\Consumer\ClearWebsiteSessionsMessage;
use App\Entity\Project;
use App\Repository\Analytics\Website\SessionRepository;
use App\Repository\ProjectRepository;
use App\Tests\KernelTestCase;

class ClearWebsiteSessionsHandlerTest extends KernelTestCase
{
    public function testConsume()
    {
        self::bootKernel();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('151f1340-9ad6-47c7-a8a5-838ff955eae7');
        $this->assertInstanceOf(Project::class, $project);

        // There should be all sessions
        $this->assertSame(190, static::getContainer()->get(SessionRepository::class)->count(['project' => $project]));

        /** @var ClearWebsiteSessionsHandler $handler */
        $handler = static::getContainer()->get(ClearWebsiteSessionsHandler::class);
        $handler(new ClearWebsiteSessionsMessage($project->getId()));

        // The sessions concerned should have been deleted
        $this->assertSame(189, static::getContainer()->get(SessionRepository::class)->count(['project' => $project]));
    }
}
