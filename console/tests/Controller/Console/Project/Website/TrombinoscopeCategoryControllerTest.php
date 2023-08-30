<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Website\TrombinoscopeCategory;
use App\Repository\Website\TrombinoscopeCategoryRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class TrombinoscopeCategoryControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertEquals(2, $crawler->filter('tbody tr:nth-child(2) td:nth-child(3)')->text());
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'trombinoscope_category[name]' => $name,
        ]);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var TrombinoscopeCategory $category */
        $category = static::getContainer()->get(TrombinoscopeCategoryRepository::class)->findOneBy(['name' => $name]);

        $this->assertCount(3, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(3) td:nth-child(2)', $name);
        $this->assertEquals(3, $category->getWeight());
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'trombinoscope_category[name]' => $name,
        ]);

        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input.is-invalid');
    }

    public function provideEdit(): iterable
    {
        yield ['Eure-et-Loir', 'Eure-et-Loir Edit'];
    }

    /**
     * @dataProvider provideEdit
     */
    public function testEdit(string $oldName, string $newName)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(2) td:nth-child(2)', $oldName);

        $link = $crawler->filter('tbody tr:nth-child(2) td:nth-child(5) a:nth-child(1)')->link();
        $crawler = $client->click($link);

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'trombinoscope_category[name]' => $newName,
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $client->click($crawler->selectLink('Delete')->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('tbody tr'));
    }

    public function provideSort()
    {
        yield [[
            [
                'id' => '4c55d963-458a-495a-85cb-67ed4a747bbe',
                'order' => 1,
            ],
            [
                'id' => 'ee760968-6581-40ad-8b4d-af073e8943a4',
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope/categories');
        $this->assertResponseIsSuccessful();

        $token = $this->filterGlobalCsrfToken($crawler);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/categories/sort?_token='.$token,
            ['data' => Json::encode($data)]
        );

        $repository = static::getContainer()->get(TrombinoscopeCategoryRepository::class);

        foreach ($data as $item) {
            /** @var TrombinoscopeCategory $category */
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope/categories');
        $this->assertResponseIsSuccessful();

        $token = $this->filterGlobalCsrfToken($crawler);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/categories/sort?_token='.$token,
            ['data' => Json::encode($data)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
