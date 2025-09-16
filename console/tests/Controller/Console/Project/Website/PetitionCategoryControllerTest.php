<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Website\LocalizedPetition;
use App\Entity\Website\Petition;
use App\Entity\Website\PetitionCategory;
use App\Repository\ProjectRepository;
use App\Repository\Website\PetitionCategoryRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PetitionCategoryControllerTest extends WebTestCase
{
    private function createCategoryFixtures(EntityManagerInterface $em, PetitionCategoryRepository $repo): array
    {
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => self::PROJECT_CITIPO_UUID]);

        $cat1 = new PetitionCategory($project, 'Programme', 1);
        $cat2 = new PetitionCategory($project, 'Proposals', 2);
        $em->persist($cat1);
        $em->persist($cat2);
        $em->flush();

        return [$cat1, $cat2];
    }

    public function testList()
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repo = static::getContainer()->get(PetitionCategoryRepository::class);
        $this->createCategoryFixtures($em, $repo);

        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $postsNumber = $crawler->filter('tbody tr:nth-child(2) td:nth-child(3)');
        $this->assertEquals(0, (int) $postsNumber->text());
    }

    public function testListCountPetitions()
    {
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => self::PROJECT_CITIPO_UUID]);

        $petition = new Petition($project, 'petition-title');
        $loc = new LocalizedPetition($petition, 'en', 'Petition Title');

        $petitionCategory = new PetitionCategory($project, 'Category', 3);
        $loc->getCategories()->add($petitionCategory);

        $em->persist($petition);
        $em->persist($loc);
        $em->persist($petitionCategory);
        $em->flush();

        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('tbody tr'));

        $postsNumber = $crawler->filter('tbody tr:nth-child(1) td:nth-child(3)');
        $this->assertEquals(1, (int) $postsNumber->text());
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        if (0 === $crawler->filter('tbody tr')->count()) {
            // ensure we have 2 categories first
            $em = static::getContainer()->get(EntityManagerInterface::class);
            $repo = static::getContainer()->get(PetitionCategoryRepository::class);
            $this->createCategoryFixtures($em, $repo);
            $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        }

        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'petition_category[name]' => $name,
        ]);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var PetitionCategory $petitionCategory */
        $petitionCategory = static::getContainer()->get(PetitionCategoryRepository::class)->findOneBy(['name' => $name]);

        $this->assertCount(3, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(3) td:nth-child(2)', $name);
        $this->assertEquals(3, $petitionCategory->getWeight());
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

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        $crawler = $client->click($crawler->selectLink('New category')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'petition_category[name]' => $name,
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
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repo = static::getContainer()->get(PetitionCategoryRepository::class);
        $this->createCategoryFixtures($em, $repo);
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(1) td:nth-child(2)', $oldName);

        $link = $crawler->filter('tbody tr:nth-child(1) td:nth-child(5) a:nth-child(1)')->link();
        $crawler = $client->click($link);

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'petition_category[name]' => $newName,
        ]);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(1) td:nth-child(2)', $newName);
    }

    public function testDelete()
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repo = static::getContainer()->get(PetitionCategoryRepository::class);
        $this->createCategoryFixtures($em, $repo);
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $client->click($crawler->selectLink('Delete')->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('tbody tr'));
    }

    public function testSort()
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repo = static::getContainer()->get(PetitionCategoryRepository::class);
        [$c1, $c2] = $this->createCategoryFixtures($em, $repo);
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        $this->assertResponseIsSuccessful();

        $token = $this->filterGlobalCsrfToken($crawler);

        $data = [
            ['id' => (string) $c1->getUuid(), 'order' => 2],
            ['id' => (string) $c2->getUuid(), 'order' => 1],
        ];

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories/sort?_token='.$token,
            ['data' => Json::encode($data)]
        );

        $repository = static::getContainer()->get(PetitionCategoryRepository::class);

        $category1 = $repository->findOneBy(['uuid' => $c1->getUuid()]);
        $category2 = $repository->findOneBy(['uuid' => $c2->getUuid()]);
        $this->assertEquals(2, $category1->getWeight());
        $this->assertEquals(1, $category2->getWeight());
    }

    public function provideSortInvalidData()
    {
        yield [[]];
        yield [[['id' => 'not-a-uuid']]];
    }

    /**
     * @dataProvider provideSortInvalidData
     */
    public function testSortInvalidData(array $data)
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repo = static::getContainer()->get(PetitionCategoryRepository::class);
        $this->createCategoryFixtures($em, $repo);
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories');
        $this->assertResponseIsSuccessful();

        $token = $this->filterGlobalCsrfToken($crawler);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_CITIPO_UUID.'/website/petitions/categories/sort?_token='.$token,
            ['data' => Json::encode($data)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
