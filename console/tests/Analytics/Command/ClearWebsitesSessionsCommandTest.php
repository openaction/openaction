<?php

namespace App\Tests\Analytics\Command;

use App\Command\Analytics\ClearWebsitesSessionsCommand;
use App\Entity\Project;
use App\Repository\Analytics\Website\SessionRepository;
use App\Repository\ProjectRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ClearWebsitesSessionsCommandTest extends KernelTestCase
{
    public function testClearsOldSessions(): void
    {
        self::bootKernel();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('151f1340-9ad6-47c7-a8a5-838ff955eae7');
        $this->assertInstanceOf(Project::class, $project);

        /** @var SessionRepository $sessionRepository */
        $sessionRepository = static::getContainer()->get(SessionRepository::class);

        // There should be all sessions
        $this->assertSame(190, $sessionRepository->count(['project' => $project]));

        $command = static::getContainer()->get(ClearWebsitesSessionsCommand::class);
        $tester = new CommandTester($command);
        $tester->execute([]);

        // The sessions concerned should have been deleted
        $this->assertSame(189, $sessionRepository->count(['project' => $project]));
    }
}
