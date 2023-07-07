<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Website\Post;
use App\Entity\Website\PostCategory;
use App\Repository\ProjectRepository;
use App\Repository\Website\PostCategoryRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PostCategoryControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $postsNumber = $crawler->filter('tbody tr:nth-child(2) td:nth-child(3)');
        $this->assertEquals(0, $postsNumber->text());
    }

    public function testListCountPosts()
    {
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => self::PROJECT_CITIPO_UUID]);

        $post = new Post($project, 'Title');

        $postCategory = new PostCategory($project, 'Category', 3);
        $postCategory->getPosts()->add($post);
        $post->getCategories()->add($postCategory);

        $em->persist($post);
        $em->persist($postCategory);

        $em->flush();

        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('tbody tr'));

        $postsNumber = $crawler->filter('tbody tr:nth-child(3) td:nth-child(3)');
        $this->assertEquals(1, $postsNumber->text());
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'post_category[name]' => $name,
        ]);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var PostCategory $postCategory */
        $postCategory = static::getContainer()->get(PostCategoryRepository::class)->findOneBy(['name' => $name]);

        $this->assertCount(3, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(3) td:nth-child(2)', $name);
        $this->assertEquals(3, $postCategory->getWeight());
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'post_category[name]' => $name,
        ]);

        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input.is-invalid');
    }

    public function provideEdit(): iterable
    {
        yield ['Programme', 'Programme Edit'];
    }

    /**
     * @dataProvider provideEdit
     */
    public function testEdit(string $oldName, string $newName)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(2) td:nth-child(2)', $oldName);

        $link = $crawler->filter('tbody tr:nth-child(2) td:nth-child(5) a:nth-child(1)')->link();
        $crawler = $client->click($link);

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'post_category[name]' => $newName,
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories');
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
                'id' => '62686dd5-33b6-476f-bedb-bfbc3a84df0d',
                'order' => 1,
            ],
            [
                'id' => '7221e3e1-df48-450d-b667-639fdc699971',
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories');
        $this->assertResponseIsSuccessful();

        $token = $this->filterGlobalCsrfToken($crawler);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories/sort?_token='.$token,
            ['data' => Json::encode($data)]
        );

        $repository = static::getContainer()->get(PostCategoryRepository::class);

        foreach ($data as $item) {
            /** @var PostCategory $category */
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories');
        $this->assertResponseIsSuccessful();

        $token = $this->filterGlobalCsrfToken($crawler);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories/sort?_token='.$token,
            ['data' => Json::encode($data)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
