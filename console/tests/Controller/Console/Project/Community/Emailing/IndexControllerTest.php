<?php

namespace App\Tests\Controller\Console\Project\Community\Emailing;

use App\Entity\Community\EmailingCampaign;
use App\Repository\AreaRepository;
use App\Repository\Community\EmailingCampaignRepository;
use App\Repository\Community\TagRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerTest extends WebTestCase
{
    private const CAMPAIGN_DRAFT_UUID = '31fedd69-2d28-4900-8088-d28ad9606a99';

    public function testCreateFromTemplate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/create-from-template');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Choose this template")');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/[0-9a-zA-Z\-]+/community/emailing/[0-9a-zA-Z\-]+/content~', $client->getResponse()->headers->get('Location'));
    }

    public function testCreateFromTemplateInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/create-from-template');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Choose this template")');
        $this->assertCount(1, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Duplicate")');
        $this->assertCount(5, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertMatchesRegularExpression('~/console/project/[0-9a-zA-Z\-]+/community/emailing/[0-9a-zA-Z\-]+/content~', $client->getResponse()->headers->get('Location'));
    }

    public function testDuplicateInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Duplicate")');
        $this->assertCount(5, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(8, $link);

        $client->click($link->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(7, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains("Delete")');
        $this->assertCount(8, $link);
        $this->assertCount(1, $crawler->filter('td:contains("Campaign with opens tracking")'));
        $this->assertCount(1, $crawler->filter('td:contains("Campaign with clicks tracking")'));
    }

    public function provideEditMetadata()
    {
        yield 'all' => [
            'name' => 'Subject',
            'preview' => 'Preview',
            'fromEmail' => 'from-email',
            'fromName' => 'From Name',
            'replyToEmail' => 'replyto@email.com',
            'replyToName' => 'Reply Name',
            'onlyForMembers' => 'false',
            'areas' => [],
            'tags' => [],
            'tagsType' => 'and',
            'contacts' => [],
        ];

        yield 'areas' => [
            'name' => 'Subject',
            'preview' => 'Preview',
            'fromEmail' => 'from-email',
            'fromName' => 'From Name',
            'replyToEmail' => 'replyto@email.com',
            'replyToName' => 'Reply Name',
            'onlyForMembers' => '0',
            'areas' => ['fr_11'],
            'tags' => [],
            'tagsType' => 'or',
            'contacts' => [],
        ];

        yield 'tags' => [
            'name' => 'Subject',
            'preview' => 'Preview',
            'fromEmail' => 'from-email',
            'fromName' => 'From Name',
            'replyToEmail' => 'replyto@email.com',
            'replyToName' => 'Reply Name',
            'onlyForMembers' => '1',
            'areas' => [],
            'tags' => ['exampletag'],
            'tagsType' => 'and',
            'contacts' => [],
        ];

        yield 'contacts' => [
            'name' => 'Subject',
            'preview' => 'Preview',
            'fromEmail' => 'from-email',
            'fromName' => 'From Name',
            'replyToEmail' => 'replyto@email.com',
            'replyToName' => 'Reply Name',
            'onlyForMembers' => 'true',
            'areas' => [],
            'tags' => [],
            'tagsType' => 'or',
            'contacts' => ['apolline.mousseau@rpr.fr', 'a.compagnon@protonmail.com'],
        ];
    }

    /**
     * @dataProvider provideEditMetadata
     */
    public function testEditMetadata(string $subject, string $preview, string $fromEmail, string $fromName, string $replyToEmail, string $replyToName, string $onlyForMembers, array $areas, array $tags, string $tagsType, array $contacts)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.self::CAMPAIGN_DRAFT_UUID.'/metadata');
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
        foreach ($tags as $k => $slug) {
            $tag = $tagRepo->findOneBy(['slug' => $slug]);
            $tagsIds[] = ['id' => $tag->getId(), 'name' => $tag->getName(), 'slug' => $tag->getSlug()];
        }

        $client->submit($crawler->selectButton('Save')->form(), [
            'emailing_campaign_meta_data[subject]' => $subject,
            'emailing_campaign_meta_data[preview]' => $preview,
            'emailing_campaign_meta_data[fromEmail]' => $fromEmail,
            'emailing_campaign_meta_data[fromName]' => $fromName,
            'emailing_campaign_meta_data[replyToEmail]' => $replyToEmail,
            'emailing_campaign_meta_data[replyToName]' => $replyToName,
            'emailing_campaign_meta_data[onlyForMembers]' => $onlyForMembers,
            'emailing_campaign_meta_data[tagsFilter]' => Json::encode($tagsIds),
            'emailing_campaign_meta_data[tagsFilterType]' => $tagsType,
            'emailing_campaign_meta_data[areasFilterIds]' => Json::encode($areasIds),
            'emailing_campaign_meta_data[contactsFilter]' => Json::encode($contacts),
        ]);

        $this->assertResponseRedirects('/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.self::CAMPAIGN_DRAFT_UUID.'/metadata');

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneBy(['uuid' => '31fedd69-2d28-4900-8088-d28ad9606a99']);
        $this->assertSame($subject, $campaign->getSubject());
        $this->assertSame($preview, $campaign->getPreview());
        $this->assertSame($fromEmail, $campaign->getFromEmail());
        $this->assertSame($fromName, $campaign->getFromName());
        $this->assertSame($replyToEmail, $campaign->getReplyToEmail());
        $this->assertSame($replyToName, $campaign->getReplyToName());
        $this->assertSame($tagsType, $campaign->getTagsFilterType());

        $this->assertSame(filter_var($onlyForMembers, FILTER_VALIDATE_BOOLEAN), $campaign->isOnlyForMembers());

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
        $this->assertSelectorExists('h3:contains("'.$subject.'")');
    }

    public function testEditContent()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.self::CAMPAIGN_DRAFT_UUID.'/content');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#campaign-editor');
    }

    public function testUpdateContent()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.self::CAMPAIGN_DRAFT_UUID.'/content');
        $this->assertResponseIsSuccessful();

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.self::CAMPAIGN_DRAFT_UUID.'/content/update',
            ['content' => 'my html', 'design' => Json::encode(['body' => []])],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        $this->assertResponseIsSuccessful();

        /** @var EmailingCampaign $campaign */
        $campaign = static::getContainer()->get(EmailingCampaignRepository::class)->findOneBy(['uuid' => self::CAMPAIGN_DRAFT_UUID]);
        $this->assertEquals('my html', $campaign->getContent());
        $this->assertEquals(['body' => []], $campaign->getUnlayerDesign());
    }

    public function testUpdateContentInvalidCsrf()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('POST', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.self::CAMPAIGN_DRAFT_UUID.'/content/update');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
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

        $client->request('POST', '/console/project/'.self::PROJECT_IDF_UUID.'/community/emailing/'.self::CAMPAIGN_DRAFT_UUID.'/content/upload', [], [
            'file' => new UploadedFile(__DIR__.'/../../../../../Fixtures/upload/'.$filename, $filename),
        ]);

        $this->assertResponseStatusCodeSame($expectedStatus);

        if ($expectedAdded) {
            $this->assertJson($json = $client->getResponse()->getContent());
            $this->assertNotEmpty(Json::decode($json)['url']);
        }

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertSame($expectedAdded, count($storage->listContents('.')->toArray()) > 0);
    }
}
