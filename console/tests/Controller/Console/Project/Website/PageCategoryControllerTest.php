<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Website\Page;
use App\Entity\Website\PageCategory;
use App\Repository\ProjectRepository;
use App\Repository\Website\PageCategoryRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PageCategoryControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request(
            'GET',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories'
        );

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $pagesNumber = $crawler->filter('tbody tr:nth-child(2) td:nth-child(3)');
        $this->assertEquals(0, $pagesNumber->text());
    }

    public function testListCountPages()
    {
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => self::PROJECT_CITIPO_UUID]);

        $page = new Page($project, 'Title');

        $pageCategory = new PageCategory($project, 'Category', 3);
        $pageCategory->getPages()->add($page);
        $page->getCategories()->add($pageCategory);

        $em->persist($page);
        $em->persist($pageCategory);

        $em->flush();

        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories');

        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('tbody tr'));

        $pagesNumber = $crawler->filter('tbody tr:nth-child(3) td:nth-child(3)');
        $this->assertEquals(1, $pagesNumber->text());
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'page_category[name]' => $name,
        ]);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var PageCategory $pageCategory */
        $pageCategory = static::getContainer()->get(PageCategoryRepository::class)->findOneBy(['name' => $name]);

        $this->assertCount(3, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(3) td:nth-child(2)', $name);
        $this->assertEquals(3, $pageCategory->getWeight());
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'page_category[name]' => $name,
        ]);

        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input.is-invalid');
    }

    public function provideEdit(): iterable
    {
        yield ['Health', 'Health Edit'];
    }

    /**
     * @dataProvider provideEdit
     */
    public function testEdit(string $oldName, string $newName)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(2) td:nth-child(2)', $oldName);

        $link = $crawler->filter('tbody tr:nth-child(2) td:nth-child(5) a:nth-child(1)')->link();
        $crawler = $client->click($link);

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'page_category[name]' => $newName,
        ]);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(2) td:nth-child(2)', $newName);
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories');
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
                'id' => 'e2c5977a-5ddd-41b6-93b8-ccc7cea925cf',
                'order' => 1,
            ],
            [
                'id' => '8c21fb5c-6566-44c5-845a-34a6f536cc7e',
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories');
        $this->assertResponseIsSuccessful();

        $token = $this->filterGlobalCsrfToken($crawler);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories/sort?_token='.$token,
            ['data' => Json::encode($data)]
        );

        $repository = static::getContainer()->get(PageCategoryRepository::class);

        foreach ($data as $item) {
            /** @var PageCategory $category */
            $category = $repository->findOneBy(['uuid' => $item['id']]);
            $this->assertEquals($item['order'], $category->getWeight());
        }
    }

    public function provideSortInvalidData()
    {
        yield [[]];
        yield [[['id' => '7221e3e1-df48-450d-b667-639fdc699971']]];
    }

    /**
     * @dataProvider provideSortInvalidData
     */
    public function testSortInvalidData(array $data)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories');
        $this->assertResponseIsSuccessful();

        $token = $this->filterGlobalCsrfToken($crawler);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories/sort?_token='.$token,
            ['data' => Json::encode($data)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
