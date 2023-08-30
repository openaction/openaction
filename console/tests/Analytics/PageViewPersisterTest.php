<?php

namespace App\Tests\Analytics;

use Analytics\PageViewPersister;
use App\Entity\Analytics\Website\Event;
use App\Entity\Analytics\Website\PageView;
use App\Entity\Project;
use App\Entity\Website\Post;
use App\Repository\Analytics\Website\EventRepository;
use App\Repository\Analytics\Website\PageViewRepository;
use App\Repository\ProjectRepository;
use App\Repository\Website\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PageViewPersisterTest extends KernelTestCase
{
    private PageViewRepository $repository;
    private EventRepository $eventRepository;
    private Project $project;

    public function setUp(): void
    {
        self::bootKernel();

        $this->repository = static::getContainer()->get(PageViewRepository::class);
        $this->eventRepository = static::getContainer()->get(EventRepository::class);
        $this->project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('643e47ea-fd9d-4963-958f-05970de2f88b');

        // Clear previous page views to ensure test stability
        $this->clearDatabase();
    }

    public function tearDown(): void
    {
        // Clear previous page views to ensure test stability
        $this->clearDatabase();
    }

    public function providePersist()
    {
        yield 'basic' => [
            'projectUuid' => '643e47ea-fd9d-4963-958f-05970de2f88b',
            'ip' => '82.65.40.45',
            'path' => '/fr/blog',
            'platform' => 'Linux',
            'browser' => 'Firefox',
            'country' => 'fr',
            'referrer' => 'twitter.com',
            'referrerPath' => '/path',
            'utmSource' => null,
            'utmMedium' => null,
            'utmCampaign' => null,
            'utmContent' => null,
            'expectedPageView' => true,
        ];

        yield 'utm' => [
            'projectUuid' => '643e47ea-fd9d-4963-958f-05970de2f88b',
            'ip' => '82.65.40.45',
            'path' => '/fr/blog',
            'platform' => 'Linux',
            'browser' => 'Firefox',
            'country' => 'fr',
            'referrer' => 'twitter.com',
            'referrerPath' => '/path',
            'utmSource' => 'twitter',
            'utmMedium' => 'email',
            'utmCampaign' => '25',
            'utmContent' => 'example',
            'expectedPageView' => true,
        ];

        yield 'ipv6' => [
            'projectUuid' => '643e47ea-fd9d-4963-958f-05970de2f88b',
            'ip' => '2a06:98c0:3600:0:0:0:0:103',
            'path' => '/fr/blog',
            'platform' => 'Linux',
            'browser' => 'Firefox',
            'country' => 'fr',
            'referrer' => 'twitter.com',
            'referrerPath' => '/path',
            'utmSource' => null,
            'utmMedium' => null,
            'utmCampaign' => null,
            'utmContent' => null,
            'expectedPageView' => true,
        ];

        yield 'nullables' => [
            'projectUuid' => '643e47ea-fd9d-4963-958f-05970de2f88b',
            'ip' => '82.65.40.45',
            'path' => '/fr/blog',
            'platform' => null,
            'browser' => null,
            'country' => null,
            'referrer' => null,
            'referrerPath' => null,
            'utmSource' => null,
            'utmMedium' => null,
            'utmCampaign' => null,
            'utmContent' => null,
            'expectedPageView' => true,
        ];

        yield 'invalid-project' => [
            'projectUuid' => 'invalid',
            'ip' => '82.65.40.45',
            'path' => '/fr/blog',
            'platform' => 'Linux',
            'browser' => 'Firefox',
            'country' => 'fr',
            'referrer' => 'twitter.com',
            'referrerPath' => '/path',
            'utmSource' => null,
            'utmMedium' => null,
            'utmCampaign' => null,
            'utmContent' => null,
            'expectedPageView' => false,
        ];
    }

    /**
     * @dataProvider providePersist
     */
    public function testPersist(string $projectUuid, string $ip, string $path, ?string $platform, ?string $browser, ?string $country, ?string $referrer, ?string $referrerPath, ?string $utmSource, ?string $utmMedium, ?string $utmCampaign, ?string $utmContent, bool $expectedPageView)
    {
        /** @var PageViewRepository $repository */
        $repository = static::getContainer()->get(PageViewRepository::class);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('643e47ea-fd9d-4963-958f-05970de2f88b');

        $persister = new PageViewPersister(static::getContainer()->getParameter('analytics_database_url'));
        $persister->persist($projectUuid, $ip, $path, $platform, $browser, $country, $referrer, $referrerPath, $utmSource, $utmMedium, $utmCampaign, $utmContent);

        if (!$expectedPageView) {
            $this->assertSame(0, $repository->count(['project' => $project]));
        } else {
            $this->assertSame(1, $repository->count(['project' => $project]));

            /** @var PageView $pageView */
            $pageView = $repository->findOneBy(['project' => $project]);

            $this->assertNotEmpty($pageView->getHash());
            $this->assertSame($path, $pageView->getPath());
            $this->assertSame($platform, $pageView->getPlatform());
            $this->assertSame($browser, $pageView->getBrowser());
            $this->assertSame($country, $pageView->getCountry());
            $this->assertSame($referrer, $pageView->getReferrer());
            $this->assertSame($referrerPath, $pageView->getReferrerPath());
            $this->assertSame($utmSource, $pageView->getUtmSource());
            $this->assertSame($utmMedium, $pageView->getUtmMedium());
            $this->assertSame($utmCampaign, $pageView->getUtmCampaign());
            $this->assertSame($utmContent, $pageView->getUtmContent());
        }
    }

    public function testIncrementPageViews()
    {
        /** @var PostRepository $repository */
        $repository = static::getContainer()->get(PostRepository::class);

        $previousPost = $repository->findOneBy(['uuid' => 'c58e4465-0168-568e-a6fd-1ed44f555bb0']);
        $this->assertInstanceOf(Post::class, $previousPost);
        $previousPageViews = $previousPost->getPageViews();

        $persister = new PageViewPersister(static::getContainer()->getParameter('analytics_database_url'));
        $persister->incrementPageViews('post', 'c58e4465-0168-568e-a6fd-1ed44f555bb0');

        static::getContainer()->get(EntityManagerInterface::class)->clear();

        $nextPost = $repository->findOneBy(['uuid' => 'c58e4465-0168-568e-a6fd-1ed44f555bb0']);
        $this->assertInstanceOf(Post::class, $nextPost);

        $this->assertSame($previousPageViews + 1, $nextPost->getPageViews());
    }

    public function testPersistEvent()
    {
        /** @var EventRepository $repository */
        $repository = static::getContainer()->get(EventRepository::class);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('643e47ea-fd9d-4963-958f-05970de2f88b');

        $persister = new PageViewPersister(static::getContainer()->getParameter('analytics_database_url'));
        $persister->persistEvent($project->getUuid()->toRfc4122(), '82.65.40.45', 'level_1');

        $this->assertSame(1, $repository->count(['project' => $project]));

        /** @var Event $event */
        $event = $repository->findOneBy(['project' => $project]);

        $this->assertNotEmpty($event->getHash());
        $this->assertSame('level_1', $event->getName());
    }

    private function clearDatabase()
    {
        $this->repository
            ->createQueryBuilder('v')
            ->delete()
            ->where('v.project = :project')
            ->setParameter('project', $this->project)
            ->getQuery()
            ->execute()
        ;

        $this->eventRepository
            ->createQueryBuilder('e')
            ->delete()
            ->where('e.project = :project')
            ->setParameter('project', $this->project)
            ->getQuery()
            ->execute()
        ;
    }
}
