<?php

namespace App\Tests\Analytics\Command;

use App\Command\Analytics\BuildWebsitesSessionsCommand;
use App\Entity\Analytics\Website\Session;
use App\Entity\Project;
use App\Repository\Analytics\Website\PageViewRepository;
use App\Repository\Analytics\Website\SessionRepository;
use App\Repository\ProjectRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Uuid;

class BuildWebsitesSessionsCommandTest extends KernelTestCase
{
    public function testBuildsSessionsFromPageViews(): void
    {
        self::bootKernel();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('e816bcc6-0568-46d1-b0c5-917ce4810a87');
        $this->assertInstanceOf(Project::class, $project);

        /** @var SessionRepository $sessionRepository */
        $sessionRepository = static::getContainer()->get(SessionRepository::class);
        /** @var PageViewRepository $pageViewRepository */
        $pageViewRepository = static::getContainer()->get(PageViewRepository::class);

        // There shouldn't be any session yet
        $this->assertNull($sessionRepository->findOneBy(['project' => $project]));

        // There should be all page views
        $this->assertSame(392, $pageViewRepository->count(['project' => $project]));

        $command = static::getContainer()->get(BuildWebsitesSessionsCommand::class);
        $tester = new CommandTester($command);
        $tester->execute([]);

        // The session should have been created
        /** @var Session $session */
        $session = $sessionRepository->findOneBy([
            'project' => $project,
            'hash' => Uuid::fromString('829c0e9e-1171-b7b8-2bcd-9c4d2484ceec'),
        ]);
        $this->assertInstanceOf(Session::class, $session);
        $this->assertSame('829c0e9e-1171-b7b8-2bcd-9c4d2484ceec', $session->getHash()->toRfc4122());
        $this->assertSame(['/', '/posts', '/'], $session->getPathsFlow());
        $this->assertSame(3, $session->getPathsCount());
        $this->assertSame('Linux', $session->getPlatform());
        $this->assertSame('Firefox', $session->getBrowser());
        $this->assertSame('fr', $session->getCountry());
        $this->assertSame('www.google.com', $session->getOriginalReferrer());
        $this->assertSame('buffer', $session->getUtmSource());
        $this->assertSame('post', $session->getUtmMedium());
        $this->assertSame('25', $session->getUtmCampaign());
        $this->assertSame('example', $session->getUtmContent());
        $this->assertSame(13, $session->getStartDate()->diff($session->getEndDate())->s);

        // The page views concerned should have been deleted
        $this->assertSame(0, $pageViewRepository->count(['project' => $project]));
    }
}
