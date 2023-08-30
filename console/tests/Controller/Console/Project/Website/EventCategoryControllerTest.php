<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Website\Event;
use App\Entity\Website\EventCategory;
use App\Repository\ProjectRepository;
use App\Repository\Website\EventCategoryRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class EventCategoryControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories');

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $eventsNumber = $crawler->filter('tbody tr:nth-child(2) td:nth-child(3)');
        $this->assertEquals(1, $eventsNumber->text());
    }

    public function testListCountEvents()
    {
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]);

        $event = new Event($project, 'Title');

        $eventCategory = new EventCategory($project, 'test event category', 3);
        $eventCategory->getEvents()->add($event);
        $event->getCategories()->add($eventCategory);

        $em->persist($event);
        $em->persist($eventCategory);
        $em->flush();

        $this->authenticate($client);
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories');
        $eventsNumber = $crawler->filter('tbody tr:nth-child(3) td:nth-child(3)');

        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('tbody tr'));
        $this->assertSelectorExists('td:contains("test event category")');
        $this->assertEquals(1, $eventsNumber->text());
    }

    public function provideCreate(): iterable
    {
        yield ['NewCategory'];
    }

    /**
     * @dataProvider provideCreate
     */
    public function testCreate(string $name)
    {
        $client = static::createClient();
        $this->authenticate($client);
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => static::PROJECT_CITIPO_UUID]);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'event_category[name]' => $name,
        ]);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var EventCategory $eventCategory */
        $eventCategory = static::getContainer()->get(EventCategoryRepository::class)->findOneBy([
            'name' => $name,
            'project' => $project->getId(),
        ]);

        $this->assertInstanceOf(EventCategory::class, $eventCategory);
        $this->assertCount(3, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(3) td:nth-child(2)', $name);
        $this->assertEquals(3, $eventCategory->getWeight());
    }

    public function provideCreateInvalid(): iterable
    {
        yield [str_pad('category', 50, '-fail')];
        yield [''];
    }

    /**
     * @dataProvider provideCreateInvalid
     */
    public function testCreateInvalid(string $name)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'event_category[name]' => $name,
        ]);

        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input.is-invalid');
    }

    public function provideEdit(): iterable
    {
        yield ['Webinars', 'Webinars new'];
    }

    /**
     * @dataProvider provideEdit
     */
    public function testEdit(string $oldName, string $newName)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $selector = 'tbody tr:nth-child(2) td:nth-child(2)';
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories');

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains($selector, $oldName);

        $link = $crawler->filter('tbody tr:nth-child(2) td:nth-child(5) a:nth-child(1)')->link();
        $crawler = $client->click($link);

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'event_category[name]' => $newName,
        ]);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains($selector, $newName);
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories');

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $client->click($crawler->selectLink('Delete')->link());
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('tbody tr'));
    }

    public function provideSort()
    {
        yield [[
            [
                'id' => 'f8d06b26-4aa3-4800-adb2-9d00880cbe17',
                'order' => 1,
            ],
            [
                'id' => '9966b3b5-901d-4609-9cf1-ffa949987043',
                'order' => 2,
            ],
        ]];
    }

    /**
     * @dataProvider provideSort
     */
    public function testSort(array $data)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories/sort?_token='.$this->filterGlobalCsrfToken($crawler),
            ['data' => Json::encode($data)]
        );

        $repository = static::getContainer()->get(EventCategoryRepository::class);

        foreach ($data as $item) {
            /** @var EventCategory $category */
            $category = $repository->findOneBy(['uuid' => $item['id']]);
            $this->assertEquals($item['order'], $category->getWeight());
        }
    }

    public function provideSortInvalidData()
    {
        yield [[]];
        yield [[['id' => '22c534af-d64e-451d-854d-02dc47c50f2e']]];
    }

    /**
     * @dataProvider provideSortInvalidData
     */
    public function testSortInvalidData(array $data)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories/sort?_token='.$this->filterGlobalCsrfToken($crawler),
            ['data' => Json::encode($data)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
