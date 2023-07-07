<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Project;
use App\Entity\Upload;
use App\Entity\Website\ManifestoTopic;
use App\Repository\ProjectRepository;
use App\Repository\Website\ManifestoTopicRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Util\Uid;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ManifestoTopicControllerTest extends WebTestCase
{
    private const TOPIC_DURABLE_UUID = '61d592f6-8435-4b7f-984a-d6b2f406c36b';
    private const TOPIC_DRAFT_UUID = 'ef98bc0f-d422-4316-a4fe-f20a7e4a7c51';

    public function testSort()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Fetch current order
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();
        $this->assertCount(4, $ids = $crawler->filter('.world-list-row')->each(fn (Crawler $row) => $row->attr('data-id')));

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
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/sort?_token='.$token,
            ['data' => Json::encode($payload)]
        );

        $this->assertResponseIsSuccessful();

        // Check order was reversed
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();
        $this->assertCount(4, $newIds = $crawler->filter('.world-list-row')->each(fn (Crawler $row) => $row->attr('data-id')));
        $this->assertSame(array_reverse($ids), $newIds);
    }

    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);
        $repository = static::getContainer()->get(ManifestoTopicRepository::class);
        $this->assertSame(4, $repository->count(['project' => $project->getId()]));

        $client->clickLink('New topic');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(5, $repository->count(['project' => $project->getId()]));
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);
        $repository = static::getContainer()->get(ManifestoTopicRepository::class);
        $this->assertSame(4, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Duplicate');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(5, $repository->count(['project' => $project->getId()]));
    }

    public function testMove()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DRAFT_UUID.'/move');
        $this->assertResponseIsSuccessful();

        /** @var Project $citipoProject */
        $citipoProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);

        $form = $crawler->selectButton('Move')->form();
        $client->submit($form, ['move_entity[into]' => $citipoProject->getId()]);

        // Check new location
        $topic = static::getContainer()->get(ManifestoTopicRepository::class)->findOneBy(['uuid' => self::TOPIC_DRAFT_UUID]);
        $this->assertSame($citipoProject->getId(), $topic->getProject()->getId());

        // Check redirect
        $location = $client->getResponse()->headers->get('Location');
        $this->assertSame('/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto', $location);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DURABLE_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'manifesto_topic[title]' => 'Renamed',
            'manifesto_topic[color]' => 'ACOLOR',
            'manifesto_topic[description]' => 'Updated desc',
        ]);

        /** @var ManifestoTopic $topic */
        $topic = static::getContainer()->get(ManifestoTopicRepository::class)->findOneBy([
            'uuid' => self::TOPIC_DURABLE_UUID,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSame('Renamed', $topic->getTitle());
        $this->assertSame('ACOLOR', $topic->getColor());
        $this->assertSame('Updated desc', $topic->getDescription());
    }

    public function testUnpublish()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $repository = static::getContainer()->get(ManifestoTopicRepository::class);

        /** @var ManifestoTopic $topic */
        $topic = $repository->findOneBy(['uuid' => self::TOPIC_DURABLE_UUID]);
        $this->assertTrue($topic->isPublished());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DURABLE_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        // Unpublish
        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DURABLE_UUID.'/save',
            ['manifesto_topic_published_at' => ['publishedAt' => '']],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var ManifestoTopic $topic */
        $topic = $repository->findOneBy(['uuid' => self::TOPIC_DURABLE_UUID]);
        $this->assertFalse($topic->isPublished());
    }

    public function testPublish()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $repository = static::getContainer()->get(ManifestoTopicRepository::class);

        /** @var ManifestoTopic $topic */
        $topic = $repository->findOneBy(['uuid' => self::TOPIC_DRAFT_UUID]);
        $this->assertFalse($topic->isPublished());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DRAFT_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DRAFT_UUID.'/save',
            ['manifesto_topic_published_at' => ['publishedAt' => '2020-01-29T16:30:44+01:00']],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var ManifestoTopic $topic */
        $topic = $repository->findOneBy(['uuid' => self::TOPIC_DRAFT_UUID]);
        $this->assertTrue($topic->isPublished());
        $this->assertSame('2020-01-29', $topic->getPublishedAt()->format('Y-m-d'));
    }

    public function testUploadImage()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $file = new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'm.png', 'image/png', null, true);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DURABLE_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DURABLE_UUID.'/image',
            [],
            ['manifesto_topic_image' => ['file' => $file]],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($payload = $client->getResponse()->getContent());

        // Check the database reference was saved
        /** @var ManifestoTopic $topic */
        $topic = static::getContainer()->get(ManifestoTopicRepository::class)->findOneBy(['uuid' => self::TOPIC_DURABLE_UUID]);
        $this->assertInstanceOf(ManifestoTopic::class, $topic);
        $this->assertInstanceOf(Upload::class, $topic->getImage());
        $this->assertStringEndsWith('/serve/'.$topic->getImage()->getPathname(), Json::decode($payload)['image']);

        // Check the file was saved in the CDN
        $this->assertTrue(static::getContainer()->get('cdn.storage')->fileExists($topic->getImage()->getPathname()));
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();
        $this->assertCount(4, $crawler->filter('.world-list-row'));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);
        $repository = static::getContainer()->get(ManifestoTopicRepository::class);
        $this->assertSame(4, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->followRedirect();
        $this->assertCount(3, $crawler->filter('.world-list-row'));
        $this->assertSame(3, $repository->count(['project' => $project->getId()]));
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/topic/'.self::TOPIC_DURABLE_UUID.'/view');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame(
            'https://localhost/_redirect/manifesto/'.Uid::toBase62(Uuid::fromString(self::TOPIC_DURABLE_UUID)),
            $client->getResponse()->headers->get('Location')
        );
    }
}
