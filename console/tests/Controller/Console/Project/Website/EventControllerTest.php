<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\Event;
use App\Entity\Website\EventCategory;
use App\Entity\Website\Form;
use App\Repository\ProjectRepository;
use App\Repository\Website\EventCategoryRepository;
use App\Repository\Website\EventRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Util\Uid;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class EventControllerTest extends WebTestCase
{
    private const EVENT_DRAFT_UUID = 'b186291d-b1ee-5458-a0f2-e31410fd26a5';

    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events');
        $this->assertResponseIsSuccessful();

        $this->assertCount(3, $crawler->filter('.world-list-row'));
    }

    public function testFilteredList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]);

        /** @var EventCategory $category */
        $category = static::getContainer()->get(EventCategoryRepository::class)->findOneBy([
            'project' => $project->getId(),
            'name' => 'Webinars',
        ]);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events?c='.$category->getId());
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.world-list-row'));
    }

    public function testEmptyList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/website/events');
        $this->assertResponseIsSuccessful();

        $this->assertCount(0, $crawler->filter('.world-list-row'));
        $this->assertSelectorExists('h4:contains("Your project does not contain any event for now.")');
        $this->assertSelectorExists('a:contains("Create the first event")');
    }

    public function provideCreateOrEditData(): iterable
    {
        yield [
            'title' => 'New title',
            'date' => new \DateTime('2020-08-01T15:30:00+00:00'),
            'address' => '1 Rue de la Convention, Paris 15e Arrondissement, Île-de-France, France',
            'latitude' => '-1.2345',
            'longitude' => '45.6789',
            'content' => 'event content',
            'url' => 'https://www.event.fr',
            'buttonText' => 'See more',
            'hasForm' => false,
        ];

        yield [
            'title' => 'Event with form register',
            'date' => new \DateTime('2020-08-01T15:30:00+00:00'),
            'address' => '1 Rue de la Convention, Paris 15e Arrondissement, Île-de-France, France',
            'latitude' => '-1.2345',
            'longitude' => '45.6789',
            'content' => 'event content',
            'url' => 'https://www.event.fr',
            'buttonText' => 'See more',
            'hasForm' => true,
        ];
    }

    /**
     * @dataProvider provideCreateOrEditData
     */
    public function testCreate(string $title, \DateTime $date, string $address, string $latitude, string $longitude, string $content, string $url, string $buttonText, bool $hasForm)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events');
        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('.world-list-row'));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]);
        $repository = static::getContainer()->get(EventRepository::class);
        $this->assertSame(3, $repository->count(['project' => $project->getId()]));

        $link = $crawler->filter('a:contains("New event")');
        $this->assertCount(1, $link);

        $crawler = $client->click($link->link());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=create_event]');

        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'create_event[title]' => $title,
            'create_event[beginAt][date][year]' => $date->format('Y'),
            'create_event[beginAt][date][month]' => $date->format('n'),
            'create_event[beginAt][date][day]' => $date->format('j'),
            'create_event[beginAt][time][hour]' => $date->format('G'),
            'create_event[beginAt][time][minute]' => $date->format('i'),
            'create_event[address]' => $address,
            'create_event[latitude]' => $latitude,
            'create_event[longitude]' => $longitude,
            'create_event[content]' => $content,
            'create_event[url]' => $url,
            'create_event[buttonText]' => $buttonText,
            'create_event[hasForm]' => $hasForm,
        ]);

        // Check redirect
        $location = $client->getResponse()->headers->get('Location');
        $this->assertMatchesRegularExpression('~/console/project/[0-9a-zA-Z\-]+/website/events/[0-9a-zA-Z\-]+/edit~', $location);

        preg_match('~([0-9a-zA-Z\-]+)/edit$~', $location, $matches);
        $event = $repository->findOneBy(['uuid' => $matches[1]]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form[name=update_event]');
        $this->assertSelectorExists('div[data-controller=event--edit]');
        $this->assertSame(4, $repository->count(['project' => $project->getId()]));

        $this->assertSame($title, $event->getTitle());
        $this->assertSame($date->format('Y/m/d H:i'), $event->getBeginAt()->format('Y/m/d H:i'));
        $this->assertSame($address, $event->getAddress());
        $this->assertSame($content, $event->getContent());

        if ($hasForm) {
            $this->assertInstanceOf(Form::class, $event->getForm());
            $this->assertStringStartsWith('https://citipo.com/_redirect/form/', $event->getUrl());
            $this->assertSame('Register', $event->getButtonText());
        } else {
            $this->assertSame($url, $event->getUrl());
            $this->assertSame($buttonText, $event->getButtonText());
        }
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events');
        $this->assertResponseIsSuccessful();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);
        $repository = static::getContainer()->get(EventRepository::class);
        $this->assertSame(3, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Duplicate');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(4, $repository->count(['project' => $project->getId()]));
    }

    public function testMove()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/'.self::EVENT_DRAFT_UUID.'/move');
        $this->assertResponseIsSuccessful();

        /** @var Project $acmeProject */
        $acmeProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);

        $form = $crawler->selectButton('Move')->form();
        $client->submit($form, ['move_entity[into]' => $acmeProject->getId()]);

        // Check new location
        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['uuid' => self::EVENT_DRAFT_UUID]);
        $this->assertSame($acmeProject->getId(), $event->getProject()->getId());

        // Check redirect
        $location = $client->getResponse()->headers->get('Location');
        $this->assertSame('/console/project/'.self::PROJECT_ACME_UUID.'/website/events', $location);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events');
        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('.world-list-row'));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]);
        $repository = static::getContainer()->get(EventRepository::class);
        $this->assertSame(3, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Delete');
        $this->assertResponseRedirects();

        // Check deletion
        $crawler = $client->followRedirect();
        $this->assertCount(2, $crawler->filter('.world-list-row'));
        $this->assertSame(2, $repository->count(['project' => $project->getId()]));
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/'.self::EVENT_DRAFT_UUID.'/view');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame(
            'https://citipo.com/_redirect/event/'.Uid::toBase62(Uuid::fromString(self::EVENT_DRAFT_UUID)),
            $client->getResponse()->headers->get('Location')
        );
    }

    /**
     * @dataProvider provideCreateOrEditData
     */
    public function testEdit(string $title, \DateTime $date, string $address, string $latitude, string $longitude, string $content, string $url, string $buttonText)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $oldEvent = clone static::getContainer()->get(EventRepository::class)->findOneBy([
            'uuid' => self::EVENT_DRAFT_UUID,
            'project' => static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]),
        ]);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/'.self::EVENT_DRAFT_UUID.'/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div[data-controller=event--edit]');
        $this->assertSelectorExists('form[name=update_event]');

        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'update_event[title]' => $title,
            'update_event[beginAt][date][year]' => $date->format('Y'),
            'update_event[beginAt][date][month]' => $date->format('n'),
            'update_event[beginAt][date][day]' => $date->format('j'),
            'update_event[beginAt][time][hour]' => $date->format('G'),
            'update_event[beginAt][time][minute]' => $date->format('i'),
            'update_event[address]' => $address,
            'update_event[latitude]' => $latitude,
            'update_event[longitude]' => $longitude,
            'update_event[content]' => $content,
            'update_event[url]' => $url,
            'update_event[buttonText]' => $buttonText,
        ]);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy([
            'uuid' => self::EVENT_DRAFT_UUID,
            'project' => static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]),
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertNotSame($oldEvent->getTitle(), $event->getTitle());
        $this->assertSame($title, $event->getTitle());
        $this->assertSame($date->format('Y/m/d H:i'), $event->getBeginAt()->format('Y/m/d H:i'));
        $this->assertSame($address, $event->getAddress());
        $this->assertSame($latitude, $event->getLatitude());
        $this->assertSame($longitude, $event->getLongitude());
        $this->assertSame($content, $event->getContent());
        $this->assertSame($url, $event->getUrl());
        $this->assertSame($buttonText, $event->getButtonText());
    }

    public function testUpdateMetadata()
    {
        $expectedDate = new \DateTime('2020-08-27T10:05:00+02:00');
        $client = static::createClient();
        $this->authenticate($client);

        /** @var EventCategory $category */
        $category = static::getContainer()->get(EventCategoryRepository::class)->findOneBy([
            'name' => 'Meetups',
            'project' => static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]),
        ]);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/'.self::EVENT_DRAFT_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/'.self::EVENT_DRAFT_UUID.'/update/metadata',
            [
                'update_event' => [
                    'publishedAt' => '2020-08-27T10:05:00+02:00',
                    'categories' => '["'.$category->getId().'"]',
                    'onlyForMembers' => '1',
                    'externalUrl' => 'https://google.com',
                ],
            ],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        /** @var Event $event */
        $event = static::getContainer()->get(EventRepository::class)->findOneBy([
            'uuid' => self::EVENT_DRAFT_UUID,
            'project' => static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSame($expectedDate->getTimestamp(), $event->getPublishedAt()->getTimestamp());
        $this->assertSame('Meetups', $event->getCategories()->first()->getName());
        $this->assertTrue($event->isOnlyForMembers());
        $this->assertSame('https://google.com', $event->getExternalUrl());
    }

    public function testUpdateImage()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/'.self::EVENT_DRAFT_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $file = new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'm.png', 'image/png', null, true);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/'.self::EVENT_DRAFT_UUID.'/update/image',
            [],
            ['event_image' => ['file' => $file]],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($payload = $client->getResponse()->getContent());

        // Check the database reference was saved
        /** @var Event $event */
        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['uuid' => self::EVENT_DRAFT_UUID]);
        $this->assertInstanceOf(Event::class, $event);
        $this->assertInstanceOf(Upload::class, $event->getImage());
        $this->assertStringEndsWith('/serve/'.$event->getImage()->getPathname(), Json::decode($payload)['image']);

        // Check the file was saved in the CDN
        $this->assertTrue(static::getContainer()->get('cdn.storage')->fileExists($event->getImage()->getPathname()));
    }
}
