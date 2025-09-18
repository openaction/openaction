<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Project;
use App\Entity\Website\Form;
use App\Repository\ProjectRepository;
use App\Repository\Website\FormRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Util\Uid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class FormControllerTest extends WebTestCase
{
    private const FORM_SUSTAINABLE_EU_UUID = 'a2b2dbd9-f0b8-435c-ae65-00bc93ad3356';
    private const FORM_EVENT = 'a2ad18d7-7cc3-4b9e-be77-900eda0262b4';

    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms');
        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('.world-list-row'));
    }

    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms');
        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('.world-list-row'));

        $link = $crawler->filter('a:contains("New form")');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/'.self::FORM_SUSTAINABLE_EU_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-form--edit-target="content"]');
    }

    public function testUpdate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Form $form */
        $form = static::getContainer()->get(FormRepository::class)->findOneByUuid(self::FORM_SUSTAINABLE_EU_UUID);
        $this->assertSame('Our Sustainable Europe', $form->getTitle());
        $this->assertSame('15 questions for a greener Europe', $form->getDescription());
        $this->assertTrue($form->proposeNewsletter());
        $this->assertFalse($form->isOnlyForMembers());
        $this->assertCount(41, $form->getBlocks());

        $payload = [
            'title' => 'Renamed',
            'description' => 'New description',
            'proposeNewsletter' => false,
            'onlyForMembers' => true,
            'redirectUrl' => 'https://citipo.com',
            'blocks' => [
                [
                    'id' => 1,
                    'type' => 'paragraph',
                    'content' => 'To gather the views of young people on environmental challenges as well as on Europe’s ecological policy',
                    'required' => false,
                    'config' => [],
                ],
                [
                    'id' => 2,
                    'type' => 'firstname',
                    'content' => 'First name',
                    'required' => true,
                    'config' => [],
                ],
                [
                    'id' => 'created-1',
                    'type' => 'select',
                    'content' => 'Country',
                    'required' => true,
                    'config' => [
                        'choices' => [
                            0 => 'France',
                            1 => 'Germany',
                            2 => 'Spain',
                        ],
                    ],
                ],
            ],
        ];

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/'.self::FORM_SUSTAINABLE_EU_UUID.'/edit');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/'.self::FORM_SUSTAINABLE_EU_UUID.'/update',
            [],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)],
            Json::encode($payload)
        );

        $this->assertResponseIsSuccessful();

        /** @var Form $form */
        $form = static::getContainer()->get(FormRepository::class)->findOneByUuid(self::FORM_SUSTAINABLE_EU_UUID);
        $this->assertSame('Renamed', $form->getTitle());
        $this->assertSame('New description', $form->getDescription());
        $this->assertFalse($form->proposeNewsletter());
        $this->assertTrue($form->isOnlyForMembers());
        $this->assertSame('https://citipo.com', $form->getRedirectUrl());
        $this->assertCount(3, $form->getBlocks());

        $this->assertSame('paragraph', $form->getBlocks()[0]->getType());
        $this->assertSame('To gather the views of young people on environmental challenges as well as on Europe’s ecological policy', $form->getBlocks()[0]->getContent());

        $this->assertSame('firstname', $form->getBlocks()[1]->getType());
        $this->assertSame('First name', $form->getBlocks()[1]->getContent());
        $this->assertTrue($form->getBlocks()[1]->isRequired());

        $this->assertSame('select', $form->getBlocks()[2]->getType());
        $this->assertSame('Country', $form->getBlocks()[2]->getContent());
        $this->assertTrue($form->getBlocks()[2]->isRequired());
        $this->assertSame(['choices' => ['France', 'Germany', 'Spain']], $form->getBlocks()[2]->getConfig());
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms');
        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('.world-list-row'));

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid('2c720420-65fd-4360-9d77-731758008497');
        $repository = static::getContainer()->get(FormRepository::class);
        $this->assertSame(10, $repository->count(['project' => $project->getId()]));

        $client->click($crawler->filter('.world-list-row:contains("Our Sustainable Europe") a:contains("Delete")')->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertCount(6, $crawler->filter('.world-list-row'));
        $this->assertSame(9, $repository->count(['project' => $project->getId()]));
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms');
        $this->assertResponseIsSuccessful();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);
        $repository = static::getContainer()->get(FormRepository::class);
        $this->assertSame(10, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Duplicate');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(11, $repository->count(['project' => $project->getId()]));
    }

    public function testMove()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/'.self::FORM_EVENT.'/move');
        $this->assertResponseIsSuccessful();

        /** @var Project $citipoProject */
        $citipoProject = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);

        $form = $crawler->selectButton('Move')->form();
        $client->submit($form, ['move_entity[into]' => $citipoProject->getId()]);

        // Check new location
        $page = static::getContainer()->get(FormRepository::class)->findOneBy(['uuid' => self::FORM_EVENT]);
        $this->assertSame($citipoProject->getId(), $page->getProject()->getId());

        // Check redirect
        $location = $client->getResponse()->headers->get('Location');
        $this->assertSame('/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms', $location);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/forms/'.self::FORM_SUSTAINABLE_EU_UUID.'/view');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame(
            'https://localhost/_redirect/form/'.Uid::toBase62(Uuid::fromString(self::FORM_SUSTAINABLE_EU_UUID)),
            $client->getResponse()->headers->get('Location')
        );
    }
}
