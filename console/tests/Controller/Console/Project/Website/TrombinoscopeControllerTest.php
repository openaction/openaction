<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\TrombinoscopePerson;
use App\Repository\ProjectRepository;
use App\Repository\Website\TrombinoscopeCategoryRepository;
use App\Repository\Website\TrombinoscopePersonRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Util\Uid;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class TrombinoscopeControllerTest extends WebTestCase
{
    private const PERSON_NLOISEAU_UUID = '790e9491-03c9-501f-897d-33709b02c3a3';
    private const PERSON_CCHABAUD_UUID = '5d01e66a-917b-5eed-b654-eaebb962c125';

    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope');
        $this->assertResponseIsSuccessful();

        $this->assertCount(5, $crawler->filter('.world-list-row'));
        $this->assertStringEndsWith('/res/images/default-person.jpg', $crawler->selectImage('Nathalie Loiseau')->image()->getUri());
    }

    public function testSort()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Fetch current order
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope');
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $ids = $crawler->filter('.world-list-row')->each(fn (Crawler $row) => $row->attr('data-id')));

        // Reverse the order
        $token = $this->filterGlobalCsrfToken($crawler);

        $payload = [];
        $i = 1;
        foreach (array_reverse($ids) as $id) {
            $payload[] = ['id' => (int) $id, 'order' => $i];
            ++$i;
        }

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/sort?_token='.$token,
            ['data' => Json::encode($payload)]
        );

        $this->assertResponseIsSuccessful();

        // Check order was reversed
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope');
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $newIds = $crawler->filter('.world-list-row')->each(fn (Crawler $row) => $row->attr('data-id')));
        $this->assertSame(array_reverse($ids), $newIds);
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#person-editor');
    }

    public function provideUpdateContent(): iterable
    {
        yield ['Title', 'my html'];
    }

    /**
     * @dataProvider provideUpdateContent
     */
    public function testUpdateContent(string $fullName, string $content)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/update/content',
            ['trombinoscope_person' => ['fullName' => $fullName, 'content' => $content]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        $person = static::getContainer()->get(TrombinoscopePersonRepository::class)->findOneBy(['uuid' => self::PERSON_NLOISEAU_UUID]);
        $this->assertEquals($fullName, $person->getFullName());
        $this->assertEquals($content, $person->getContent());
    }

    public function providUpdateContentInvalid()
    {
        yield 'name_empty' => ['', '', ''];
        yield 'name_invalid_length' => [str_repeat('x', 300), '', ''];
    }

    /**
     * @dataProvider providUpdateContentInvalid
     */
    public function testUpdateContentInvalidData(string $fullName, string $content)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/update/content',
            ['trombinoscope_person' => ['fullName' => $fullName, 'content' => $content]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateContentInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/update/content');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function provideUpdateMetadata(): iterable
    {
        yield [
            [
                'role' => 'Role',
                'socialEmail' => 'email@gmail.com',
                'socialFacebook' => 'https://facebook.com',
                'socialTwitter' => 'https://twitter.com',
                'socialInstagram' => 'https://instagram.com',
                'socialLinkedIn' => 'https://linkedin.com',
                'socialYoutube' => 'https://youtube.com',
                'socialMedium' => 'https://medium.com',
                'socialTelegram' => 'username',
            ],
        ];
    }

    /**
     * @dataProvider provideUpdateMetadata
     */
    public function testUpdateMetadata(array $metadata)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/update/metadata',
            ['trombinoscope_person' => $metadata],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        $person = static::getContainer()->get(TrombinoscopePersonRepository::class)->findOneBy(['uuid' => self::PERSON_NLOISEAU_UUID]);
        $this->assertSame($metadata['role'], $person->getRole());
        $this->assertSame($metadata['socialEmail'], $person->getSocialEmail());
        $this->assertSame($metadata['socialFacebook'], $person->getSocialFacebook());
        $this->assertSame($metadata['socialTwitter'], $person->getSocialTwitter());
        $this->assertSame($metadata['socialInstagram'], $person->getSocialInstagram());
        $this->assertSame($metadata['socialLinkedIn'], $person->getSocialLinkedIn());
        $this->assertSame($metadata['socialYoutube'], $person->getSocialYoutube());
        $this->assertSame($metadata['socialMedium'], $person->getSocialMedium());
        $this->assertSame($metadata['socialTelegram'], $person->getSocialTelegram());
    }

    public function provideUploadImage(): iterable
    {
        yield 'pdf' => [
            'count' => 1,
            'filename' => 'document.pdf',
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedAdded' => false,
        ];

        yield 'png' => [
            'count' => 2,
            'filename' => 'mario.png',
            'expectedStatus' => Response::HTTP_OK,
            'expectedAdded' => true,
        ];

        yield 'jpg' => [
            'count' => 3,
            'filename' => 'french.jpg',
            'expectedStatus' => Response::HTTP_OK,
            'expectedAdded' => true,
        ];
    }

    /**
     * @dataProvider provideUploadImage
     */
    public function testUploadImage(int $count, string $filename, int $expectedStatus, bool $expectedAdded)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/content/upload?count='.$count, [
            'hidimg-'.$count => base64_encode(file_get_contents(__DIR__.'/../../../../Fixtures/upload/'.$filename)),
            'hidname-'.$count => 'file',
            'hidtype-'.$count => 'png',
        ]);

        $this->assertResponseStatusCodeSame($expectedStatus);

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertSame($expectedAdded, count($storage->listContents('.')->toArray()) > 0);
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope');
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $crawler->filter('.world-list-row'));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('2c720420-65fd-4360-9d77-731758008497');
        $repository = static::getContainer()->get(TrombinoscopePersonRepository::class);
        $this->assertSame(5, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Delete');
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertCount(4, $crawler->filter('.world-list-row'));
        $this->assertSame(4, $repository->count(['project' => $project->getId()]));
    }

    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope');
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $crawler->filter('.world-list-row'));

        $link = $crawler->filter('a:contains("Add a person")');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
    }

    public function testUpdateMetadataInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('POST', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/update/metadata');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateImage()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $file = new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'm.png', 'image/png', null, true);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/update/image',
            [],
            ['trombinoscope_person_image' => ['file' => $file]],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($payload = $client->getResponse()->getContent());

        // Check the database reference was saved
        /** @var TrombinoscopePerson $person */
        $person = static::getContainer()->get(TrombinoscopePersonRepository::class)->findOneBy(['uuid' => self::PERSON_NLOISEAU_UUID]);
        $this->assertInstanceOf(TrombinoscopePerson::class, $person);
        $this->assertInstanceOf(Upload::class, $person->getImage());
        $this->assertStringEndsWith('/serve/'.$person->getImage()->getPathname(), Json::decode($payload)['image']);

        // Check the file was saved in the CDN
        $this->assertTrue(static::getContainer()->get('cdn.storage')->fileExists($person->getImage()->getPathname()));
    }

    public function provideCategories(): iterable
    {
        yield [
            'categories' => ['Loire-Atlantique'],
        ];

        yield [
            'categories' => ['Loire-Atlantique', 'Eure-et-Loir'],
        ];

        yield [
            'categories' => [],
        ];
    }

    /**
     * @dataProvider provideCategories
     */
    public function testUpdateCategories(array $categories)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_CCHABAUD_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $repo = static::getContainer()->get(TrombinoscopeCategoryRepository::class);

        $ids = [];
        foreach ($categories as $category) {
            $ids[] = $repo->findOneBy(['name' => $category])->getId();
        }

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_CCHABAUD_UUID.'/update/metadata',
            ['trombinoscope_person' => ['categories' => Json::encode($ids)]],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var TrombinoscopePerson $person */
        $person = static::getContainer()->get(TrombinoscopePersonRepository::class)->findOneByUuid(self::PERSON_CCHABAUD_UUID);
        $this->assertCount(count($categories), $person->getCategories());
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope');
        $this->assertResponseIsSuccessful();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);
        $repository = static::getContainer()->get(TrombinoscopePersonRepository::class);
        $this->assertSame(5, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Duplicate');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(6, $repository->count(['project' => $project->getId()]));
    }

    public function testMove()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/move');
        $this->assertResponseIsSuccessful();

        /** @var Project $citipoProject */
        $citipoProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);

        $form = $crawler->selectButton('Move')->form();
        $client->submit($form, ['move_entity[into]' => $citipoProject->getId()]);

        // Check new location
        $person = static::getContainer()->get(TrombinoscopePersonRepository::class)->findOneBy(['uuid' => self::PERSON_NLOISEAU_UUID]);
        $this->assertSame($citipoProject->getId(), $person->getProject()->getId());

        // Check redirect
        $location = $client->getResponse()->headers->get('Location');
        $this->assertSame('/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope', $location);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/trombinoscope/'.self::PERSON_NLOISEAU_UUID.'/view');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame(
            'https://localhost/_redirect/trombinoscope/'.Uid::toBase62(Uuid::fromString(self::PERSON_NLOISEAU_UUID)),
            $client->getResponse()->headers->get('Location')
        );
    }
}
