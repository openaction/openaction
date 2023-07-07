<?php

namespace App\Tests\Controller\Console\Project\Community;

use App\Entity\Community\PhoningCampaign;
use App\Repository\AreaRepository;
use App\Repository\Community\PhoningCampaignRepository;
use App\Repository\Community\TagRepository;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use App\Util\Uid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class PhoningControllerTest extends WebTestCase
{
    private const CAMPAIGN_ACTIVE_UUID = '186314e6-7097-4ad6-9ba1-82030892fcf0';
    private const CAMPAIGN_DRAFT_UUID = 'e5a632df-4960-4d56-bc94-944e0879268e';

    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("New phone campaign")');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/[0-9a-zA-Z\-]+/community/phoning/[0-9a-zA-Z\-]+/metadata~', $client->getResponse()->headers->get('Location'));
    }

    public function testCreateInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("New phone campaign")');
        $this->assertCount(1, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(1, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $client->click($link->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("index.drafts.delete")');
        $this->assertCount(0, $link);
    }

    public function provideEditMetadata()
    {
        yield 'all' => [
            'name' => 'something',
            'endAfter' => 72,
            'onlyForMembers' => 'true',
            'areas' => ['fr_11'],
            'tags' => [],
            'tagsType' => 'or',
            'contacts' => [],
        ];
    }

    /**
     * @dataProvider provideEditMetadata
     */
    public function testEditMetadata(string $name, int $endAfter, string $onlyForMembers, array $areas, array $tags, string $tagsType, array $contacts)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning/'.self::CAMPAIGN_DRAFT_UUID.'/metadata');
        $this->assertResponseIsSuccessful();

        // Areas filter
        $areaRepo = static::getContainer()->get(AreaRepository::class);
        $areasIds = [];
        foreach ($areas as $code) {
            $area = $areaRepo->findOneBy(['code' => $code]);
            $areaId = $area->getId();
            $areasIds[$areaId] = ['id' => $areaId, 'name' => $area->getName()];
        }

        // Tags filter
        $tagRepo = static::getContainer()->get(TagRepository::class);
        $tagsIds = [];
        foreach ($tags as $slug) {
            $tag = $tagRepo->findOneBy(['slug' => $slug]);
            $tagsIds[] = ['id' => $tag->getId(), 'name' => $tag->getName(), 'slug' => $tag->getSlug()];
        }

        $client->submit($crawler->selectButton('Save')->form(), [
            'phoning_campaign_meta_data[name]' => $name,
            'phoning_campaign_meta_data[endAfter]' => $endAfter,
            'phoning_campaign_meta_data[onlyForMembers]' => $onlyForMembers,
            'phoning_campaign_meta_data[tagsFilter]' => Json::encode($tagsIds),
            'phoning_campaign_meta_data[tagsFilterType]' => $tagsType,
            'phoning_campaign_meta_data[areasFilterIds]' => Json::encode($areasIds),
            'phoning_campaign_meta_data[contactsFilter]' => Json::encode($contacts),
        ]);

        $this->assertResponseRedirects('/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning/'.self::CAMPAIGN_DRAFT_UUID.'/metadata');

        /** @var PhoningCampaign $campaign */
        $campaign = static::getContainer()->get(PhoningCampaignRepository::class)->findOneBy(['uuid' => self::CAMPAIGN_DRAFT_UUID]);
        $this->assertSame($name, $campaign->getName());
        $this->assertSame($endAfter, $campaign->getEndAfter());
        $this->assertSame($tagsType, $campaign->getTagsFilterType());

        if ($contacts) {
            sort($contacts);

            $contactsFilter = $campaign->getContactsFilter();
            sort($contactsFilter);
            $this->assertSame($contacts, $contactsFilter);
        } else {
            $this->assertNull($campaign->getContactsFilter());
        }

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[value="'.$name.'"]');
    }

    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(1, $link);

        $this->assertCount(1, $crawler->filter('h4:contains("Draft campaign")'));
        $this->assertCount(1, $crawler->filter('td:contains("Active campaign")'));
        $this->assertCount(1, $crawler->filter('td:contains("Finished campaign")'));
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning');
        $this->assertResponseIsSuccessful();

        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);
        $repository = static::getContainer()->get(PhoningCampaignRepository::class);
        $this->assertSame(3, $repository->count(['project' => $project->getId()]));

        $client->clickLink('Duplicate');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning/[0-9a-zA-Z\-]+/metadata~', $client->getResponse()->headers->get('Location'));
        $this->assertSame(4, $repository->count(['project' => $project->getId()]));
    }

    public function testView()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/phoning/'.self::CAMPAIGN_ACTIVE_UUID.'/view');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame(
            'https://localhost/_redirect/phoning/'.Uid::toBase62(Uuid::fromString(self::CAMPAIGN_ACTIVE_UUID)),
            $client->getResponse()->headers->get('Location')
        );
    }
}
