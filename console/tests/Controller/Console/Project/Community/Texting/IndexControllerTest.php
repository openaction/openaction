<?php

namespace App\Tests\Controller\Console\Project\Community;

use App\Entity\Community\TextingCampaign;
use App\Repository\AreaRepository;
use App\Repository\Community\TagRepository;
use App\Repository\Community\TextingCampaignRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/texting');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(3, $link);
    }

    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/texting');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("New texting campaign")');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/[0-9a-zA-Z\-]+/community/texting/[0-9a-zA-Z\-]+/edit~', $client->getResponse()->headers->get('Location'));
    }

    public function testCreateInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/texting');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("New texting campaign")');
        $this->assertCount(1, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/texting');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(3, $link);

        $client->click($link->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(2, $link);
    }

    public function testDeleteInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/texting');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(3, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function provideEdit()
    {
        yield 'all' => [
            'content' => 'lorem ipsum',
            'onlyForMembers' => 'false',
            'areas' => [],
            'tags' => [],
            'tagsType' => 'and',
            'contacts' => [],
        ];

        yield 'areas' => [
            'content' => 'dolor sit amet',
            'onlyForMembers' => 'false',
            'areas' => ['fr_11'],
            'tags' => [],
            'tagsType' => 'or',
            'contacts' => [],
        ];

        yield 'tags' => [
            'content' => 'donec ut nisi quis arcu',
            'onlyForMembers' => 'true',
            'areas' => [],
            'tags' => ['exampletag'],
            'tagsType' => 'and',
            'contacts' => [],
        ];

        yield 'contacts' => [
            'content' => 'sed est consequat',
            'onlyForMembers' => 'true',
            'areas' => [],
            'tags' => [],
            'tagsType' => 'or',
            'contacts' => ['+55 61 99881-2130'],
        ];
    }

    /**
     * @dataProvider provideEdit
     */
    public function testEdit(string $content, string $onlyForMembers, array $areas, array $tags, string $tagsType, array $contacts)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/texting/e4a3799d-b217-4389-b89e-beef08bdbbd3/edit');
        $this->assertResponseIsSuccessful();

        // Areas filter
        $areaRepo = self::getContainer()->get(AreaRepository::class);
        $areasIds = [];
        foreach ($areas as $code) {
            $area = $areaRepo->findOneBy(['code' => $code]);
            $areaId = $area->getId();
            $areasIds[$areaId] = ['id' => $areaId, 'name' => $area->getName()];
        }

        // Tags filter
        $tagRepo = self::getContainer()->get(TagRepository::class);
        $tagsIds = [];
        foreach ($tags as $k => $slug) {
            $tag = $tagRepo->findOneBy(['slug' => $slug]);
            $tagsIds[] = ['id' => $tag->getId(), 'name' => $tag->getName(), 'slug' => $tag->getSlug()];
        }

        $client->submit($crawler->selectButton('Save')->form(), [
            'texting_campaign_meta_data[content]' => $content,
            'texting_campaign_meta_data[onlyForMembers]' => $onlyForMembers,
            'texting_campaign_meta_data[tagsFilter]' => Json::encode($tagsIds),
            'texting_campaign_meta_data[tagsFilterType]' => $tagsType,
            'texting_campaign_meta_data[areasFilterIds]' => Json::encode($areasIds),
            'texting_campaign_meta_data[contactsFilter]' => Json::encode($contacts),
        ]);

        /** @var TextingCampaign $campaign */
        $campaign = self::getContainer()->get(TextingCampaignRepository::class)->findOneBy(['uuid' => 'e4a3799d-b217-4389-b89e-beef08bdbbd3']);
        $this->assertSame($content, $campaign->getContent());
        $this->assertSame($tagsType, $campaign->getTagsFilterType());

        $this->assertSame(filter_var($onlyForMembers, FILTER_VALIDATE_BOOLEAN), $campaign->isOnlyForMembers());

        if ($contacts) {
            sort($contacts);

            $contactsFilter = $campaign->getContactsFilter() ?: [];
            sort($contactsFilter);
            $this->assertSame($contacts, $contactsFilter);
        } else {
            $this->assertNull($campaign->getContactsFilter());
        }

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
