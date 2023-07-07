<?php

namespace App\Tests\Analytics\Consumer;

use App\Analytics\Consumer\BuildWebsiteSessionsHandler;
use App\Analytics\Consumer\BuildWebsiteSessionsMessage;
use App\Entity\Analytics\Website\Session;
use App\Entity\Project;
use App\Repository\Analytics\Website\PageViewRepository;
use App\Repository\Analytics\Website\SessionRepository;
use App\Repository\ProjectRepository;
use App\Tests\KernelTestCase;
use Symfony\Component\Messenger\Transport\TransportInterface;

class BuildWebsiteSessionsHandlerTest extends KernelTestCase
{
    public function testConsume()
    {
        self::bootKernel();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('e816bcc6-0568-46d1-b0c5-917ce4810a87');
        $this->assertInstanceOf(Project::class, $project);

        // There shouldn't be any session yet
        $this->assertNull(static::getContainer()->get(SessionRepository::class)->findOneBy(['project' => $project]));

        // There should be all page views
        $this->assertSame(392, static::getContainer()->get(PageViewRepository::class)->count(['project' => $project]));

        /** @var BuildWebsiteSessionsHandler $handler */
        $handler = static::getContainer()->get(BuildWebsiteSessionsHandler::class);
        $handler(new BuildWebsiteSessionsMessage($project->getId()));

        // The session should have been created
        /** @var Session $session */
        $session = static::getContainer()->get(SessionRepository::class)->findOneBy(['project' => $project]);
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
        $this->assertSame(389, static::getContainer()->get(PageViewRepository::class)->count(['project' => $project]));

        // A new session should be built right away as there are sessions to build
        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');

        $messages = $transport->get();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(BuildWebsiteSessionsMessage::class, $messages[0]->getMessage());
        $this->assertSame($project->getId(), $messages[0]->getMessage()->getProjectId());
    }

    public function testConsumeNoPageViews()
    {
        self::bootKernel();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('2c720420-65fd-4360-9d77-731758008497');
        $this->assertInstanceOf(Project::class, $project);

        /** @var BuildWebsiteSessionsHandler $handler */
        $handler = static::getContainer()->get(BuildWebsiteSessionsHandler::class);
        $handler(new BuildWebsiteSessionsMessage($project->getId()));

        // No session should have been created
        $this->assertNull(static::getContainer()->get(SessionRepository::class)->findOneBy(['project' => $project]));

        // No new session should be built
        $this->assertCount(0, static::getContainer()->get('messenger.transport.async_priority_low')->get());
    }
}
