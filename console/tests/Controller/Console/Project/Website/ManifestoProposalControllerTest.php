<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Website\ManifestoProposal;
use App\Repository\Website\ManifestoProposalRepository;
use App\Repository\Website\ManifestoTopicRepository;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ManifestoProposalControllerTest extends WebTestCase
{
    private const TOPIC_DURABLE_UUID = '61d592f6-8435-4b7f-984a-d6b2f406c36b';
    private const PROPOSAL_COMMUTE_UUID = '85a12e9e-921e-43a1-a12d-630de3656510';

    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();

        $topic = static::getContainer()->get(ManifestoTopicRepository::class)->findOneByUuid(self::TOPIC_DURABLE_UUID);
        $repository = static::getContainer()->get(ManifestoProposalRepository::class);
        $this->assertSame(2, $repository->count(['topic' => $topic->getId()]));

        $client->click($crawler->filter('.world-list-row .manifesto-proposals a:contains("New proposal")')->first()->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/proposal/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(3, $repository->count(['topic' => $topic->getId()]));
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();

        $topic = static::getContainer()->get(ManifestoTopicRepository::class)->findOneByUuid(self::TOPIC_DURABLE_UUID);
        $repository = static::getContainer()->get(ManifestoProposalRepository::class);
        $this->assertSame(2, $repository->count(['topic' => $topic->getId()]));

        $client->click($crawler->filter('.world-list-row .manifesto-proposals a:contains("Duplicate")')->first()->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/proposal/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(3, $repository->count(['topic' => $topic->getId()]));
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/proposal/'.self::PROPOSAL_COMMUTE_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#proposal-title');
    }

    public function testSave()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/proposal/'.self::PROPOSAL_COMMUTE_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto/proposal/'.self::PROPOSAL_COMMUTE_UUID.'/save',
            [
                'manifesto_proposal' => [
                    'title' => 'Renamed title',
                    'content' => 'Renamed content',
                    'status' => 'in_progress',
                    'statusDescription' => 'Renamed description',
                    'statusCtaText' => 'Renamed cta',
                    'statusCtaUrl' => 'https://citipo.com',
                ],
            ],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var ManifestoProposal $proposal */
        $proposal = static::getContainer()->get(ManifestoProposalRepository::class)->findOneBy(['uuid' => self::PROPOSAL_COMMUTE_UUID]);
        $this->assertSame('Renamed title', $proposal->getTitle());
        $this->assertSame('Renamed content', $proposal->getContent());
        $this->assertSame('in_progress', $proposal->getStatus());
        $this->assertSame('Renamed description', $proposal->getStatusDescription());
        $this->assertSame('Renamed cta', $proposal->getStatusCtaText());
        $this->assertSame('https://citipo.com', $proposal->getStatusCtaUrl());
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/manifesto');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-list-row:contains("Pour une ville plus durable") .manifesto-proposals [data-id]'));

        $topic = static::getContainer()->get(ManifestoTopicRepository::class)->findOneByUuid(self::TOPIC_DURABLE_UUID);
        $repository = static::getContainer()->get(ManifestoProposalRepository::class);
        $this->assertSame(2, $repository->count(['topic' => $topic->getId()]));

        $client->click($crawler->filter('.world-list-row .manifesto-proposals a:contains("Delete")')->first()->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('.world-list-row:contains("Pour une ville plus durable") .manifesto-proposals [data-id]'));
        $this->assertSame(1, $repository->count(['topic' => $topic->getId()]));
    }
}
